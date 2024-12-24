<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/public
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */
class Cqrc_Generator_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		if (has_shortcode($post->post_content, 'cqrc_gen_qrcode_view')) {
			$style_url = add_query_arg( 'ver', $this->version, CQRCGEN_PUBLIC_URL . '/assets/css/cqrc-generator-public.css' );
			wp_enqueue_style( $this->plugin_name, $style_url, array(), time(), 'all' );
		}
	}

	/**
	 * QRCode Download Option handle.
	 * @since 1.0.0
	 */
	public function cqrc_handle_qr_code_download() {
		if ( !empty($_GET['action']) && $_GET['action'] === 'download_qr' ) {
			
			if(empty($_GET['custom'])) {
				// Verify the nonce before processing further
				if (empty($_GET['download_qr_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['download_qr_nonce'])), 'download_qr_nonce')) {
					wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator'));
				}
			}
			
			$id = !empty($_GET['id']) ? absint($_GET['id']) : 0;
			$type = !empty($_GET['type']) ? sanitize_text_field(wp_unslash($_GET['type'])) : '';

        	// Validate file type
			if ( ! in_array( $type, array( 'png', 'jpg', 'pdf' ), true ) ) {
				wp_die( esc_html__('Invalid file type.', 'custom-qr-code-generator' ) );
			}
			
			global $wpdb;
			$table_name = QRCODE_GENERATOR_TABLE;

        	// Retrieve QR Code URL
			$qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", $id ) );// phpcs:ignore

			if ( ! $qr_code_row ) {
				wp_die( esc_html__('QR code not found.', 'custom-qr-code-generator') );
			}

			$qr_code_url = esc_url($qr_code_row->qr_code);
			$qr_code_name = esc_html($qr_code_row->name);
			$file_extension = esc_html($type);

			// Generate PDF using FPDF
			if ( $type === 'pdf' ) {

				if ( !class_exists( '\\Dompdf\\Dompdf' ) ) {
					return;
				}

				$dompdf = new \Dompdf\Dompdf();
				$response = wp_remote_get( $qr_code_url );
				
				if ( is_wp_error( $response ) ) {
					wp_die( esc_html__('Error fetching image.', 'custom-qr-code-generator' ) );
				}
				
				// Retrieve the body of the response
				$image = wp_remote_retrieve_body( $response );
				
				// Encode the image to Base64
				$base64_image = 'data:image/png;base64,' . base64_encode($image);

				$html = '<h1>Title - ' . esc_html($qr_code_row->name) . '</h1><img src="' . $base64_image . '" width="700" height="830">'; // phpcs:ignore
				
				$dompdf->loadHtml($html);
				
				// Set paper size and orientation
				$dompdf->setPaper('A4', 'portrait');
				
				// Render the PDF
				$dompdf->render();
				
				// Output the generated PDF (force download)
				$filename = 'qrcode-' . $qr_code_name . '-' . $id . '.pdf'; 
				$dompdf->stream($filename, array("Attachment" => true));
				exit;
			}

			// Construct the file name
			$file_name = 'qrcode-'.strtolower(str_replace(' ','-',$qr_code_name)).'-' . $id .'.'. $file_extension;
			
			// Set headers
			header( 'Content-Type: image/' . esc_attr($file_extension) );
			header( 'Content-Disposition: attachment; filename="' . esc_attr($file_name) . '"' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
			
			// Fetch image content
			$response = wp_remote_get( $qr_code_url );
			
			// Check for errors
			if ( is_wp_error( $response ) ) {
				wp_die( esc_html__('Error fetching image.', 'custom-qr-code-generator' ) );
			}
			
			// Retrieve the body of the response
			$image = wp_remote_retrieve_body( $response );
			
			// Output the image content
			echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			wp_die();
		}
	}

	public function cqrc_register_qrcode_shortcode() {
		add_shortcode( 'cqrc_gen_qrcode_view', array( $this, 'cqrc_qrcode_shortcode_handler' ) );
	}

	/**
	 * Handle the Generated QR Code custom URL request
	 */
	public function cqrc_qrcode_template_redirect() {
		if ( get_query_var('qrcode_scan') ) {

			// Verify the nonce before processing further
			if ( empty( $_REQUEST['qrcode_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['qrcode_wpnonce'] ) ), 'qrcode_scan_nonce') ) {
				wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator'));
			}
			
			global $wpdb;
			$generator_table = QRCODE_GENERATOR_TABLE;
			$table_name = QRCODE_INSIGHTS_TABLE; 

			// Get decrypted query parameters
			$new_url = !empty($_GET['url']) ? sanitize_text_field(wp_unslash($_GET['url'])) : '';
			$new_qrid = !empty($_GET['qrid']) ? sanitize_text_field(wp_unslash($_GET['qrid'])) : '';
			$previd = !empty($_GET['previd']) ? sanitize_text_field(wp_unslash($_GET['previd'])) : '';
			$token = !empty($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
			$message = '';
			$url = hex2bin($new_url);
			$qrid = intval(substr($new_qrid, 0, -3));   
			$user_ip = !empty($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
			$device_type = cqrc_get_device_type();
			$location = cqrc_get_user_location($user_ip);

			$request_method = !empty($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '';

			if (!empty($token) && !empty($token) && !empty($_GET['qrcode_wpnonce'])) {
				$plugins_page_url = site_url();

				if ($request_method === 'POST' && !empty($_POST['password'])) {
					$password = sanitize_text_field(wp_unslash($_POST['password']));
					$query = $wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE token = %s AND password = %s", $token, $password); // phpcs:ignore

					if ($wpdb->get_var($query)) { // phpcs:ignore
						$data = array(
							'user_ip_address' => $user_ip,
							'device_type'     => $device_type,
							'location'        => json_encode($location), // phpcs:ignore
							'qrid'            => $qrid,
							'created_at'      => current_time('mysql'),
						);
						$format = array('%s', '%s', '%s', '%d', '%s');
						$existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
						$update = false; 
						if ($existing_record == 0) {
							$wpdb->insert($table_name, $data, $format); // phpcs:ignore
							$scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
							$update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d'));  // phpcs:ignore
						}   
						if ($update !== false) {
							wp_redirect($url);
							exit;
						} else {
							cqrc_display_error_message();
						}
					} else {
						$message = '<p style="color: red;">Invalid password. Please try again.</p>';
					}
				}
				cqrc_display_password_form($message);
				wp_die();
			}

			$query    = $wpdb->prepare( "SELECT token FROM $generator_table WHERE id = %d", $qrid ); // phpcs:ignore
			$qrixists = $wpdb->get_var($query); // phpcs:ignore
			
			if (!empty($qrixists) && !empty($qrixists)) {
				$plugins_page_url = site_url();
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
					$password = sanitize_text_field(wp_unslash($_POST['password']));
					$query = $wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE token = %s AND password = %s", $qrixists, $password); // phpcs:ignore

					if ($wpdb->get_var($query)) { // phpcs:ignore
						$data = array(
							'user_ip_address' => $user_ip,
							'device_type'     => $device_type,
							'location'        => json_encode($location), // phpcs:ignore
							'qrid'            => $qrid,
							'created_at'      => current_time('mysql'),
						);
						$format = array('%s', '%s', '%s', '%d', '%s');
						$existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
						if ($existing_record == 0) {
							$wpdb->insert($table_name, $data, $format); // phpcs:ignore
							$scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
							$update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore
						}
						if ($update !== false) {
							wp_redirect($url);
							exit;
						} else {
							cqrc_display_error_message();
						}
					} else {
						$message = '<p style="color: red;">Invalid password. Please try again.</p>';
					}
				}
				cqrc_display_password_form($message);
				wp_die();
			}
			// Check if Previous option disable
			if (!empty($previd) && $previd !== '') {
				cqrc_display_previous_error_message();
			}

			// Check if QRID is empty
			if ($qrid == '') {
				cqrc_display_error_message();
			}

			// Check if QRID exists in the generator table
			$qrid_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE id = %d", $qrid)); // phpcs:ignore
			
			if ($qrid_exists == 0) {
				cqrc_display_error_message();
			}

			// Check if the same QRID and IP address exist in the insights table
			$existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore

			if ($existing_record == 0) {
				// If no record exists, insert the new record
				$data = array(
					'user_ip_address' => $user_ip,
					'device_type'     => $device_type,
					'location'        => json_encode($location), // phpcs:ignore
					'qrid'            => $qrid,
					'created_at'      => current_time('mysql'),
				);
				$format = array('%s', '%s', '%s', '%d', '%s');

				// Insert data and update scan count
				$inserted = $wpdb->insert($table_name, $data, $format); // phpcs:ignore
				if ($inserted !== false) {
					// Get the total scans for the QRID
					$scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
					// Update the total_scans in the generator table
					$update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore

					if ($update !== false) {
						wp_redirect($url);
						exit;
					} else {
						cqrc_display_error_message();
					}
				} else {
					cqrc_display_error_message();
				}
			} else {
				// Record exists, just redirect. For third-party URL redirection, we utilize the wp_redirect method.
				wp_redirect($url);
				exit;
			}
		}
	}

	/**
	 * Register custom query variable
	 */
	public function cqrc_qrcode_query_vars( $vars ) {
		$vars[] = 'qrcode_scan';
		return $vars;
	}
	// Handle the QR code shortcode output
	public function cqrc_qrcode_shortcode_handler( $atts ) {
		global $wpdb;

    	// Set default attributes for the shortcode
		$atts = shortcode_atts( array(
			'id' => '',
		), $atts, 'cqrc_gen_qrcode_view' );

    	// Nonce for download QR functionality
		$download_qr_nonce = wp_create_nonce( 'download_qr_nonce' );
		$id = intval( $atts['id'] );

    	// Fetch QR code settings from the database
		$general_table = esc_sql( QRCODE_GENERATOR_TABLE );
		$general_settings = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $general_table WHERE id = %d LIMIT 1", $id ) );  // phpcs:ignore

		if ( ! $general_settings ) {
			return '<p>' . esc_html__( 'QR code not found.', 'custom-qr-code-generator' ) . '</p>';
		}

    	// Extract relevant data
		$title = !empty( $general_settings->name ) ? $general_settings->name : '';
		$description = !empty( $general_settings->description ) ? unserialize( $general_settings->description ) : '';
		$download_options = !empty( $general_settings->download ) ? $general_settings->download : '';
		$download_content = json_decode( $general_settings->download_content, true );
		$download_text_png = isset( $download_content['png'] ) ? $download_content['png'] : '';
		$download_text_jpg = isset( $download_content['jpg'] ) ? $download_content['jpg'] : '';
		$download_text_pdf = isset( $download_content['pdf'] ) ? $download_content['pdf'] : '';
		$download_options_array = is_array( $download_options ) ? $download_options : explode( ',', $download_options );

		$download_png_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'png', 'download_qr_nonce' => $download_qr_nonce ), home_url( '/download-qr/' ) ) );
		$download_jpg_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'jpg', 'download_qr_nonce' => $download_qr_nonce ), home_url( '/download-qr/' ) ) );
		$download_pdf_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'pdf', 'download_qr_nonce' => $download_qr_nonce ), home_url( '/download-qr/' ) ) );

		$qr_code_url = $this->cqrc_generate_qr_code_url( $id );

		if ( ! $qr_code_url ) {
			return '<p>' . esc_html__( 'QR code not found.', 'custom-qr-code-generator' ) . '</p>';
		}

		ob_start();
		?>
		<div class="wwt-qrcode-container">
			<div class="qr-code-showing-preview-image-fronted">
				<img src="<?php echo esc_url( $qr_code_url );  // phpcs:ignore ?>" alt="<?php esc_attr_e( 'QR Code', 'custom-qr-code-generator' ); ?>">
			</div>

			<?php if ( ! empty( $title ) ) : ?>
				<div class="qr-code-main-title">
					<h2 class="title"><?php echo esc_attr( $title ); ?></h2>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $description ) ) : ?>
				<div class="qr-code-description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php if ( in_array( 'png', $download_options_array ) || in_array( 'jpg', $download_options_array ) || in_array( 'pdf', $download_options_array ) ) : ?>
			<div class="download-qr-code-column">
				<?php if ( in_array( 'png', $download_options_array ) && ! empty( $download_text_png ) ) : ?>
				<a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_png_url ); ?>"><?php echo esc_html( $download_text_png ); ?></a>
			<?php endif; ?>

			<?php if ( in_array( 'jpg', $download_options_array ) && ! empty( $download_text_jpg ) ) : ?>
			<a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_jpg_url ); ?>"><?php echo esc_html( $download_text_jpg ); ?></a>
			<?php endif; ?>

			<?php if ( in_array( 'pdf', $download_options_array ) && ! empty( $download_text_pdf ) ) : ?>
			<a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_pdf_url ); ?>"><?php echo esc_html( $download_text_pdf ); ?></a>
		<?php endif; ?>
		</div>
		<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	// Function to generate the QR code URL from the database
	public function cqrc_generate_qr_code_url( $id ) {
		global $wpdb;
		$generator_table = esc_sql( QRCODE_GENERATOR_TABLE );

		if ( ! empty( $id ) ) {
			$qrcode_image_path = $wpdb->get_var( $wpdb->prepare( "SELECT `qr_code` FROM $generator_table WHERE id = %d", $id ) );  // phpcs:ignore

			if ( ! empty( $qrcode_image_path ) ) {
				return $qrcode_image_path;
			}
		}
		return false;
	}
}
