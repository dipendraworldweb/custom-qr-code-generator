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
			wp_enqueue_style( $this->plugin_name, $style_url, array(), null, 'all' );
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
					wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
				}
			}
			
			$id = !empty($_GET['id']) ? absint($_GET['id']) : 0;
			$type = !empty($_GET['type']) ? sanitize_text_field(wp_unslash($_GET['type'])) : '';

        	// Validate file type
			if ( ! in_array( $type, array( 'png', 'jpg', 'pdf' ), true ) ) {
				wp_die( esc_html__('Invalid file type.', 'custom-qrcode-generator' ) );
			}
			
			global $wpdb;
			$table_name = QRCODE_GENERATOR_TABLE;

        	// Retrieve QR Code URL
			$qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", $id ) );// phpcs:ignore

			if ( ! $qr_code_row ) {
				wp_die( esc_html__('QR code not found.', 'custom-qrcode-generator') );
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
					wp_die( esc_html__('Error fetching image.', 'custom-qrcode-generator' ) );
				}

            	// Retrieve the body of the response
				$image = wp_remote_retrieve_body( $response );

            	// Encode the image to Base64
				$base64_image = 'data:image/png;base64,' . base64_encode($image);

				$html = '<h1>Title - ' . esc_html($qr_code_row->name) . '</h1><img src="' . $base64_image . '"/ width="700" height="830">';
				
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
				wp_die( esc_html__('Error fetching image.', 'custom-qrcode-generator' ) );
			}
			
			// Retrieve the body of the response
			$image = wp_remote_retrieve_body( $response );
			
			// Output the image content
			echo $image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			wp_die();
		}
	}
}