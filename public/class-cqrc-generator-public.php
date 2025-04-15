<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.2
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
	 * @since    1.0.2
	 */
	public function cqrc_enqueue_styles() {
		global $post;
		if ( get_query_var('qrcode_scan') || ( ! empty( $post->post_content ) && has_shortcode( $post->post_content, 'cqrc_gen_qrcode_view' ) ) ) {
			wp_enqueue_style( $this->plugin_name, CQRCGEN_PUBLIC_URL . '/assets/css/cqrc-generator-public.css', array(), $this->version, 'all' );
		}
		// Register and Enqueue JavaScript
		wp_enqueue_script( $this->plugin_name . '-embed', CQRCGEN_PUBLIC_URL . '/assets/js/embed-qrcode.js', array('jquery'), $this->version, true );

		// Localize the script with new data
		wp_localize_script( $this->plugin_name . '-embed', 'website_url', array(
			'site_url' => get_home_url(),
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'qr_code_nonce' ),
		));
		
	}

	/**
	 * QRCode Download Option handle.
	 * @since 1.0.2
	 */
	public function cqrc_handle_qr_code_download() {
		if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'download_qr' ) {
			if( empty( $_GET['custom'] ) ) {
				// Verify the nonce before processing further
				if ( empty( $_GET['download_qr_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['download_qr_nonce'] ) ), 'download_qr_nonce' ) ) {
					wp_die( esc_html__( 'Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator' ) );
				}
			}
			
			$id   = ! empty( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : 0;
			$type = ! empty( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

        	// Validate file type
			if ( ! in_array( $type, array( 'png', 'jpg', 'pdf' ), true ) ) {
				wp_die( esc_html__( 'Invalid file type.', 'custom-qr-code-generator' ) );
			}
			
			global $wpdb;
			$table_name  = QRCODE_GENERATOR_TABLE;

        	// Retrieve QR Code URL
			$qr_code_query = $wpdb->prepare( "SELECT `name`, `description`, `qr_code`, `download_content` FROM `$table_name` WHERE `id` = %d", $id ); // phpcs:ignore
			$qr_code_row   = $wpdb->get_row( $qr_code_query ); // phpcs:ignore

			if ( empty( $qr_code_row ) ) {
				/* translators: %s is the QR code ID. */
				$message = esc_html__( 'We couldn\'t find a QR code associated with the ID "%s".', 'custom-qr-code-generator' );
				$message = sprintf( $message, $id );
				wp_die( esc_html( $message ) );
			}

			$qr_code_url = esc_url( $qr_code_row->qr_code );
			$response    = wp_remote_get( $qr_code_url );

			if ( is_wp_error( $response ) ) {
				wp_die( esc_html__( 'Error fetching image.', 'custom-qr-code-generator' ) );
			}
			
			// Retrieve the body of the response
			$image = wp_remote_retrieve_body( $response );
			if ( empty( $image ) ) {
				wp_die( esc_html__( 'QR Code image could not be fetch.', 'custom-qr-code-generator' ) );
			}

			$qr_code_name     = $qr_code_row->name;
			$unserialize_desc = wp_kses_post( $qr_code_row->description ); // phpcs:ignore
			$download_content = json_decode( $qr_code_row->download_content, true );
			$show_desc_in_pdf = !empty( $download_content['show_desc_in_pdf'] ) ? $download_content['show_desc_in_pdf'] : '';
			$file_extension   = ( 'pdf' === $type ) ? 'pdf' : esc_html( $type );
			$file_name        = 'qrcode-' . sanitize_title( $qr_code_name ) . '-' . $id . '.' . $file_extension;
			if ( is_serialized( $unserialize_desc ) && is_serialized_string( $unserialize_desc ) ) {
				$unserialize_desc = maybe_unserialize( $unserialize_desc );
			}
			
			// Generate PDF using FPDF
			if ( 'pdf' === $type ) {
				if ( ! class_exists( '\\Dompdf\\Dompdf' ) ) {
					return;
				}
				
				$dompdf       = new \Dompdf\Dompdf();
				// Encode the image to Base64
				$base64_image = 'data:image/png;base64,' . base64_encode( $image );
				$html         = '<h1>Title - ' . esc_html( $qr_code_name ) . '</h1><img src="' . $base64_image . '" width="700" height="830"><br>'; // phpcs:ignore

				if ( $show_desc_in_pdf === 'yes' ) {
					$html .= '<p><b>Description - </b>' . $unserialize_desc . '</p>'; // phpcs:ignore
				}

				$dompdf->loadHtml( $html );
				
				// Set paper size and orientation
				$dompdf->setPaper( 'A4', 'portrait' );
				
				// Render the PDF
				$dompdf->render();
				
				// Output the generated PDF (force download)
				$dompdf->stream( $file_name, array( "Attachment" => true ) );
				exit;
			}
			else {
				// Set headers
				header( 'Content-Type: image/' . esc_attr( $file_extension ) );
				header( 'Content-Disposition: attachment; filename="' . esc_attr( $file_name ) . '"' );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );
				
				// Output the image content
				echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				wp_die();
			}
		}
	}

	public function cqrc_register_qrcode_shortcode() {
		add_shortcode( 'cqrc_gen_qrcode_view', array( $this, 'cqrc_qrcode_shortcode_handler' ) );
	}

	/**
	 * Handle the Generated QR Code custom URL request
	 */
	public function cqrc_qrcode_template_redirect( $template ) {
		if ( get_query_var( 'qrcode_scan' ) ) {
			$custom_template = CQRCGEN_DIR. '/template-parts/content-cqrc-password-form.php';
			if ( file_exists( $custom_template ) ) {
				return $custom_template;
			}
		}
		return $template;
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
		$id                = absint( $atts['id'] );

    	// Fetch QR code settings from the database
		$general_table     = esc_sql( QRCODE_GENERATOR_TABLE );
		$general_settings  = $wpdb->get_row( $wpdb->prepare( "SELECT `name`, `description`, `qr_code`,`download`, `download_content` FROM `$general_table` WHERE `id` = %d LIMIT 1", $id ) );  // phpcs:ignore

		if ( ! $general_settings ) {
			/* translators: %s is the QR code ID. */
			$message = esc_html__( 'We couldn\'t find a QR code associated with the ID "%s".', 'custom-qr-code-generator' );
			$message = sprintf( $message, $id );
			return '<p class="qrcode-not-found-error-wrap">' . $message . '</p>';
		}

    	// Extract relevant data
		$title                  = ! empty( $general_settings->name ) ? $general_settings->name : '';
		$description            = ! empty( $general_settings->description ) ? maybe_unserialize( $general_settings->description ) : '';
		$download_options       = ! empty( $general_settings->download ) ? $general_settings->download : '';
		$download_content       = json_decode( $general_settings->download_content, true );
		$download_text_png      = !empty( $download_content['png'] ) ? $download_content['png'] : '';
		$download_text_jpg      = !empty( $download_content['jpg'] ) ? $download_content['jpg'] : '';
		$download_text_pdf      = !empty( $download_content['pdf'] ) ? $download_content['pdf'] : '';
		$download_options_array = is_array( $download_options ) ? $download_options : explode( ',', $download_options );
		$download_qr_code_url   = home_url( '/download-qr/' );
		$download_png_url       = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'png', 'download_qr_nonce' => $download_qr_nonce ), $download_qr_code_url ) );
		$download_jpg_url       = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'jpg', 'download_qr_nonce' => $download_qr_nonce ), $download_qr_code_url ) );
		$download_pdf_url       = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'pdf', 'download_qr_nonce' => $download_qr_nonce ), $download_qr_code_url ) );
		$qr_code_url            = esc_url( $general_settings->qr_code );
		$attachment_id 			= ( $qr_code_url !== '' ) ? attachment_url_to_postid( $qr_code_url ) : '';
		
		ob_start();
		if ( $attachment_id !== '' ) {
			?>
			<div class="wwt-qrcode-container">
				<div class="qr-code-showing-preview-image-fronted">
					<?php echo wp_get_attachment_image( $attachment_id, 'full', false, array( 'alt' => esc_attr__( 'QR Code', 'custom-qr-code-generator' ) ) ); ?>
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
}
else {
	/* translators: %s is the QR code ID. */
	$message = esc_html__( 'We couldn\'t find a QR code associated with the ID "%s".', 'custom-qr-code-generator' );
	$message = sprintf( $message, $id );
	return '<p class="qrcode-not-found-error-wrap">' . esc_html( $message ) . '</p>';
}

return ob_get_clean();
}

	/**
	 * Retrieves the QR code URL from the database based on the secure code.
	 * @since 1.0.2	 
	 */
	public function cqrc_get_qr_code_by_secure_code($hash) {
		global $wpdb;

	    // Validate parameter
		if ( empty($hash) || !is_string($hash) ) {
			return false;
		}

	    // Ensure table name is safe
		$table_name = esc_sql(QRCODE_GENERATOR_TABLE);

	    // Prepare the query to get the QR code URL where secure_code matches the given hash
	    $query = $wpdb->prepare("SELECT `qr_code` FROM `{$table_name}` WHERE `secure_code` = %s", $hash); // phpcs:ignore
	    return $wpdb->get_var($query); // phpcs:ignore
	}

	public function cqrc_register_rest_api_routes() {
    // Register the custom REST API route
		register_rest_route('cqrc/v1', '/get-qr-code/', array(
			'methods' => 'POST',
			'callback' => array($this,'cqrc_get_qr_code_by_hash_callback'),
			'permission_callback' => '__return_true',
		));
	}

	/**
	 * Handles the AJAX request to retrieve a QR code URL by its hash.
	 * @since 1.0.2
	 */
	public function cqrc_get_qr_code_by_hash_callback(WP_REST_Request $request) {

		if (empty($request['_ajax_nonce']) && !wp_verify_nonce($request['_ajax_nonce'], 'qr_code_nonce')) {
			return new WP_REST_Response('Nonce verification failed. Please refresh and try again.', 400);
		}

    	// Check if 'hash' is provided in the request (GET or POST)
		$hash = sanitize_text_field($request->get_param('hash'));

		if (empty($hash)) {
			return new WP_REST_Response(['message' => 'Invalid request'], 400);
		}

    	// Retrieve the QR code URL using the new function
		$qrcode_url = $this->cqrc_get_qr_code_by_secure_code($hash);

    	// If no QR code URL is found, return an error response
		if (!$qrcode_url) {
			return new WP_REST_Response(['message' => 'QR Code not found'], 404);
		}

    	// Return the QR code URL as a JSON response
		return new WP_REST_Response(['qrcode_url' => $qrcode_url], 200);
	}
	
}