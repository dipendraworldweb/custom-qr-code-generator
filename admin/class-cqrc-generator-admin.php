<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/admin
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */
class Cqrc_Generator_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->add_hooks();
	}

	/**
	 * Adding Hooks
	 *
	 * @return void
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'cqrc_generator_form_handle' ) );
		add_action( 'init', array( $this, 'cqrc_handle_qr_code_download' ) );
		add_action( 'init', array( $this, 'cqrc_handle_qr_code_delete_action' ) );
		add_action(	'wp_ajax_cqrc_handle_qrurl_insert_record', array( $this, 'cqrc_handle_qrurl_insert_record') );
		add_action(	'wp_ajax_nopriv_cqrc_handle_qrurl_insert_record', array( $this, 'cqrc_handle_qrurl_insert_record') );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cqrc_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cqrc_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cqrc-generator-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cqrc_Generator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cqrc_Generator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
// 		        if ( 'toplevel_page_custom-qrcode-generate-form' == $hook ) {
		        if ( 'toplevel_page_custom-qrcode-generator' == $hook || 'qr-code_page_custom-qrcode-generate-form' == $hook) {
            wp_enqueue_script( 
                $this->plugin_name . '-admin', 
                plugin_dir_url( __FILE__ ) . 'js/cqrc-generator-admin.js', 
                array( 'jquery' ), 
                $this->version, 
                false 
            );

			// Localize the script with data.
			wp_localize_script(
				$this->plugin_name . '-admin',
				'cqrcGenerator',
				array(
					'pluginLogoImagePath'     => CQRCGEN_URL . 'admin/assets/qrcode/logos/',
					'pluginFrameImagePath'    => CQRCGEN_URL . 'admin/assets/qrcode/frames/',
					'pluginTemplateImagePath' => CQRCGEN_URL . 'admin/assets/qrcode/qr-templates/',
					'pluginEyeFrameImagePath' => CQRCGEN_URL . 'admin/assets/qrcode/eye-frames/',
					'pluginEyeBallsImagePath' => CQRCGEN_URL . 'admin/assets/qrcode/eye-balls/',
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'    => wp_create_nonce('qr_code_nonce'),
					'downloadUrl' => esc_url(home_url('/download-qr/'))
				)
			);
		}
		
	}

	/**
	 * QRCode Helper function to handle the qrcode previour data.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_handle_qrurl_insert_record() {
		check_ajax_referer('qr_code_nonce', '_ajax_nonce');
		global $wpdb;

		$site_url = site_url();
		$table_name = $wpdb->prefix . 'qrcode_generator';

		if (empty($_POST['qrcode_url'])) {
			wp_send_json_error(['message' => 'Invalid URL.']);
			return;
		}
		$data = [
			'urls' => isset($_POST['urls']) ? sanitize_url(wp_unslash($_POST['urls'])) : '',
			'qrid' => isset($_POST['qrid']) ? sanitize_text_field(wp_unslash($_POST['qrid'])) : '',
			'name' => isset($_POST['qrcode_name']) ? sanitize_text_field(wp_unslash($_POST['qrcode_name'])) : '',
			'template_name' => isset($_POST['template_name']) ? sanitize_text_field(wp_unslash($_POST['template_name'])) : '',
			'logo_option' => isset($_POST['logo_option']) ? sanitize_text_field(wp_unslash($_POST['logo_option'])) : '',
			'upload_logo_url' => isset($_POST['upload_logo_url']) ? sanitize_url(wp_unslash($_POST['upload_logo_url'])) : '',
			'default_logo' => isset($_POST['default_logo']) ? sanitize_text_field(wp_unslash($_POST['default_logo'])) : '',
			'default_frame' => isset($_POST['default_frame']) ? sanitize_text_field(wp_unslash($_POST['default_frame'])) : '',
			'eye_frame_name' => isset($_POST['eye_frame_name']) ? sanitize_text_field(wp_unslash($_POST['eye_frame_name'])) : '',
			'eye_balls_name' => isset($_POST['eye_balls_name']) ? sanitize_text_field(wp_unslash($_POST['eye_balls_name'])) : '',
			'qrcode_level' => isset($_POST['qrcode_level']) ? sanitize_text_field(wp_unslash($_POST['qrcode_level'])) : 'QR_ECLEVEL_Q',
			'qr_eye_frame_color' => isset($_POST['qr_eye_frame_color']) ? sanitize_hex_color(wp_unslash( $_POST['qr_eye_frame_color'])) : '',
			'qr_eye_color' => isset($_POST['qr_eye_color']) ? sanitize_hex_color(wp_unslash( $_POST['qr_eye_color'])) : '',
			'qr_code_color' => isset($_POST['qr_code_color']) ? sanitize_hex_color(wp_unslash( $_POST['qr_code_color'])) : ''
		];

    	// Determine settings based on template
		$settings = $this->cqrc_get_qrcode_settings($data['template_name']);
		$settings = array_merge($settings, [
			'default_logo' => $data['default_logo'] ?: $settings['default_logo'],
			'default_frame' => $data['default_frame'] ?: $settings['default_frame'],
			'eye_frame_name' => $data['eye_frame_name'] ?: $settings['eye_frame_name'],
			'eye_balls_name' => $data['eye_balls_name'] ?: $settings['eye_balls_name'],
			'qr_code_color' => $data['qr_code_color'] ?: $settings['qr_code_color'],
			'qr_eye_color' => $data['qr_eye_color'] ?: $settings['qr_eye_color'],
			'qr_eye_frame_color' => $data['qr_eye_frame_color'] ?: $settings['qr_eye_frame_color'],
			'qrcode_level' => $data['qrcode_level'] ?: $settings['qrcode_level']
		]);

    	// Get file paths
		$paths = $this->cqrc_get_file_paths($data, $settings);

    	// Check if we are creating a new QR code or updating an existing one
		if (empty($data['qrid'])) {
			$result = $this->cqrc_generate_process($table_name, $data, $paths, $site_url);
		} else {
			$result = $this->cqrc_generate_process($table_name, $data, $paths, $site_url, $data['qrid']);
		}
		
		// Check the result and respond accordingly
		if ($result['success']) {
			wp_send_json_success([
				'message' => $result['message'],
				'url_data' => $result['url_data'],
				'ext_id' => $result['ext_id']
			]);
		} else {
			wp_send_json_error(['message' => $result['message']]);
		}
	}

	/**
	 * QRCode Helper function to get qrcode settings data.
	 *
	 * @since    1.0.0
	 */
	private function cqrc_get_qrcode_settings($template_name) {
		$settings = [
			'facebook' => [
				'default_logo' => __('facebook', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('frame14', 'custom-qrcode-generator'),
				'eye_balls_name' => __('ball18', 'custom-qrcode-generator'),
				'qr_code_color' => __('#2c4270', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#2c4270', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#2c4270', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_H', 'custom-qrcode-generator')
			],
			'youtube-circle' => [
				'default_logo' => __('youtube-circle', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('frame13', 'custom-qrcode-generator'),
				'eye_balls_name' => __('ball14', 'custom-qrcode-generator'),
				'qr_code_color' => __('#BF2626', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#EE0F0F', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#EE0F0F', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_Q', 'custom-qrcode-generator')
			],
			'twitter-circle' => [
				'default_logo' => __('twitter-circle', 'custom-qrcode-generator' ),
				'default_frame' => __('default', 'custom-qrcode-generator' ),
				'eye_frame_name' => __('frame5', 'custom-qrcode-generator' ),
				'eye_balls_name' => __('ball11', 'custom-qrcode-generator' ),
				'qr_code_color' => __('#55ACEE', 'custom-qrcode-generator' ),
				'qr_eye_color' => __('#55ACEE', 'custom-qrcode-generator' ),
				'qr_eye_frame_color' => __('#55ACEE', 'custom-qrcode-generator' ),
				'qrcode_level' => __('QR_ECLEVEL_Q', 'custom-qrcode-generator' )
			],
			'instagram-circle' => [
				'default_logo' => __('instagram-circle', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('frame5', 'custom-qrcode-generator'),
				'eye_balls_name' => __('ball4', 'custom-qrcode-generator'),
				'qr_code_color' => __('#0d1766', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#0d1766', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#8224e3', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_H', 'custom-qrcode-generator')
			],
			'whatsapp-circle' => [
				'default_logo' => __('whatsapp-circle', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('frame2', 'custom-qrcode-generator'),
				'eye_balls_name' => __('ball2', 'custom-qrcode-generator'),
				'qr_code_color' => __('#2ebd38', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#2ebd38', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#2ebd38', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_M', 'custom-qrcode-generator')
			],
			'gmail' => [
				'default_logo' => __('gmail', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('frame14', 'custom-qrcode-generator'),
				'eye_balls_name' => __('ball14', 'custom-qrcode-generator'),
				'qr_code_color' => __('#e4594c', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#e4594c', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#e4594c', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_Q', 'custom-qrcode-generator')
			],
			'linkedin-circle' => [
				'default_logo' => __('linkedin-circle', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('frame0', 'custom-qrcode-generator'),
				'eye_balls_name' => __('ball0', 'custom-qrcode-generator'),
				'qr_code_color' => __('#005881', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#005881', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#005881', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_M', 'custom-qrcode-generator')
			],
			'default' => [
				'default_logo' => __('default', 'custom-qrcode-generator'),
				'default_frame' => __('default', 'custom-qrcode-generator'),
				'eye_frame_name' => __('default', 'custom-qrcode-generator'),
				'eye_balls_name' => __('default', 'custom-qrcode-generator'),
				'qr_code_color' => __('#000000', 'custom-qrcode-generator'),
				'qr_eye_color' => __('#000000', 'custom-qrcode-generator'),
				'qr_eye_frame_color' => __('#000000', 'custom-qrcode-generator'),
				'qrcode_level' => __('QR_ECLEVEL_M', 'custom-qrcode-generator')
			]
		];

		return $settings[$template_name] ?? $settings['default'];
	}

	/**
	 * QRCode Helper function to get file paths based on input data.
	 *
	 * @since    1.0.0
	 */
	private function cqrc_get_file_paths($data, $settings) {
		$paths = [
			'frame' => 'default' == $data['default_frame'] ? CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/default.png' : CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/' . $data['default_frame'] . '.png',
			'logo' => $this->cqrc_get_logo_path($data, $settings),
			'eye_frame' => 'default' == $data['eye_frame_name'] ? CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/frame0.png' : CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/' . $data['eye_frame_name'] . '.png',
			'eye_balls' => 'default' == $data['eye_balls_name'] ? CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/ball0.png' : CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/' . $data['eye_balls_name'] . '.png'
		];

		return $paths;
	}

	/**
	 * QRCode Helper function to get the logo path.
	 *
	 * @since    1.0.0
	 */
	private function cqrc_get_logo_path($data, $settings) {
		if ('default' === $data['logo_option']) {
			return 'default' === $data['default_logo'] ? 'no' : CQRCGEN_ADMIN_DIR . '/assets/qrcode/logos/' . $data['default_logo'] . '.png';
		} elseif ('upload' === $data['logo_option']) {
			return $data['upload_logo_url'] ?: $uploaded_logo;
		}
		return '';
	}

	/**
	 * QRCode Helper function to process QR code creation and database update.
	 *
	 * @since    1.0.0
	 */
	private function cqrc_generate_process($table_name, $data, $paths, $site_url, $qrid = null) {
		global $wpdb;
		if (!empty($qrid)) {
			
			$prev_qrcode = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}qrcode_generator ORDER BY id DESC LIMIT 1")); // phpcs:ignore
			$prev_id = $prev_qrcode ? $prev_qrcode->id : 0;
			$identifier_with_suffix = $prev_id . 'FTA';
			$url = $site_url . '/qrcode_scan?url=' . bin2hex($data['urls']) . '&qrid=' . $identifier_with_suffix;
		}else{
			$prev_id = 0;
			$url = $site_url . '/qrcode_scan?url=' . bin2hex($data['urls']) . '&previd=PREV001';
		}

		$qr_code_url = $this->cqrc_handle_qr_code_generate_action(
			$url,
			$prev_id,
			$paths['logo'],
			$paths['frame'],
			$paths['eye_frame'],
			$paths['eye_balls'],
			$data['qr_eye_color'],
			$data['qr_eye_frame_color'],
			$data['qr_code_color'],
			$data['qrcode_level']
		);
		
 		// Check if QR code generation was successful
		if (!empty($qr_code_url)) {
        	// Update the database if QRID is provided
			// phpcs:disable
			if (!empty($qrid)) {
				$update_result = $wpdb->update(
					$table_name,
					['name' => $data['name'], 'url' => $data['urls']],
					['id' => $qrid],
					['%s', '%s'],
					['%d']
				);

            	// Check if the update was successful
				if ($update_result === false) {
					return ['success' => false, 'message' => __('Database update failed.', 'custom-qrcode-generator')];
				}
			}
			// phpcs:enable
        	// Return the successful response with QR code URL and QRID
			return [
				'success' => true,
				'message' => __('QR code processed successfully.', 'custom-qrcode-generator' ),
				'url_data' => $qr_code_url,
				'ext_id' => $qrid
			];
		}

		return ['success' => false, 'message' => __('QR code generation failed.', 'custom-qrcode-generator')];
	}

	/**
	 * QRCode generation form submission handle.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_handle_qr_code_generate_action( $url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level ) {

		global $wpdb;
		$merged_image_resource = '';
		$table_name = $wpdb->prefix . 'qrcode_generator';

		// Include PHP QR Code library.
		include_once CQRCGEN_INCLUDES_DIR . '/phpqrcode/qrlib.php';

		// QR Code Black & White Combination Fixes.
		switch ( $qr_code_color ) {
			case '#ffffff':
			$qr_code_bg_color = 0;
			$qr_eye_color     = '#ffffff';
			break;
			case '#000000':
			$qr_code_bg_color = 16777215;
			break;
			default:
			$qr_code_bg_color = 16777215;
		}

		$qr_eye_frame_color = $this->cqrc_hex_to_rgb( $qr_eye_frame_color );
		
		// Convert hex color codes to integers.
		if ($qr_code_color !== '') {
			$qr_code_color = hexdec( ltrim( $qr_code_color, '#' ) );
		}

		$qr_eye_rgb = $this->cqrc_hex_to_rgb( $qr_eye_color );
		
		// QR Code Level Constants Convertion.
		switch ( $qrcode_level ) {
			case 'QR_ECLEVEL_Q':
			$qrcode_level = QR_ECLEVEL_Q;
			break;
			case 'QR_ECLEVEL_H':
			$qrcode_level = QR_ECLEVEL_H;
			break;
			case 'QR_ECLEVEL_M':
			$qrcode_level = QR_ECLEVEL_M;
			break;
			default:
			$qrcode_level = QR_ECLEVEL_H;
		}
		
		// Generate QR code and save it to a temporary file.
		ob_start();
		$my_qr = QRcode::png( $url, null, $qrcode_level, 15, 1, false, $qr_code_bg_color, $qr_code_color );
		
		$qr_image = ob_get_clean();
		
		if ($qr_image === false) {
			die( esc_html__('Failed to get QR code image data.', 'custom-qrcode-generator') );
		}

		$qr_image_resource = imagecreatefromstring($qr_image);
		
		if ($qr_image_resource === false) {
			die( esc_html__('Failed to create image from string.', 'custom-qrcode-generator') );
		}

		$qr_width = imagesx($qr_image_resource);
		$qr_height = imagesy($qr_image_resource);
		
		if ($qr_width === false || $qr_height === false) {
			die( esc_html__('Failed to get image dimensions.', 'custom-qrcode-generator' ) );
		}

		$eyeFrame = '';
		if ( ! empty( $eye_frame_image ) ) {
		// Load the custom frame for the eyes.
			$eyeFrame = imagecreatefrompng( $eye_frame_image );
		}else{
			$eye_frame_image = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/frame0.png';
			$eyeFrame = imagecreatefrompng( $eye_frame_image );
		}
		
		// Get dimensions of the eye frame.
		$eyeFrameWidth  = imagesx( $eyeFrame );
		$eyeFrameHeight = imagesy( $eyeFrame );
		

		// Define the desired scale factor for the eye frames (e.g., 1.5 for 150% size).
		$scaleFactor = 2.1;

		// Calculate the new dimensions of the eye frame.
		$scaledEyeFrameWidth  = $eyeFrameWidth * $scaleFactor;
		$scaledEyeFrameHeight = $eyeFrameHeight * $scaleFactor;

		// Create a new true color image for the scaled eye frame.
		$scaledEyeFrame = imagecreatetruecolor( $scaledEyeFrameWidth, $scaledEyeFrameHeight );
		
		// Enable transparency for the new image.
		$test1 = imagealphablending( $scaledEyeFrame, false );
		$test2 = imagesavealpha( $scaledEyeFrame, true );

		// Resize the eye frame to the new dimensions.
		$test3 = imagecopyresampled(
			$scaledEyeFrame,
			$eyeFrame,
			0,
			0,
			0,
			0,
			$scaledEyeFrameWidth,
			$scaledEyeFrameHeight,
			$eyeFrameWidth,
			$eyeFrameHeight
		);

		// Apply color to the eye frame.
		$test4 = imagefilter( $scaledEyeFrame, IMG_FILTER_COLORIZE, $qr_eye_frame_color['r'], $qr_eye_frame_color['g'], $qr_eye_frame_color['b'], 0 );
		// }
		
		$eyeImage = '';
		if ( ! empty( $eye_image ) ) {
    		// Load the eyeball image.
			$eyeImage = imagecreatefrompng( $eye_image );
		}else{
			$eye_image = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/ball0.png';
			$eyeImage = imagecreatefrompng( $eye_image );

		}
		
    	// Apply color to the eyeball.
		imagefilter( $eyeImage, IMG_FILTER_COLORIZE, $qr_eye_rgb['r'], $qr_eye_rgb['g'], $qr_eye_rgb['b'], 0 );

    	// Get dimensions of the eyeball image.
		$eyeImageWidth  = imagesx( $eyeImage );
		$eyeImageHeight = imagesy( $eyeImage );
		
		// Define the rotation values for each eye frame image name
		if (!empty($eye_frame_image)) {
			$eye_name = basename($eye_frame_image);

			switch ($eye_name) {
				case 'frame1.png':
				$eyeRotations = array(90, 0, 180);
				break;
				case 'frame2.png':
				$eyeRotations = array(90, 0, 0);
				break;
				case 'frame3.png':
				$eyeRotations = array(270, 180, 0);
				break;
				case 'frame5.png':
				$eyeRotations = array(90, 0, 180);
				break;					
				case 'frame6.png':
				$eyeRotations = array(0, 90, 270);
				break;					
				case 'frame14.png':
				$eyeRotations = array(0, 270, 90);
				break;
				default:
				$eyeRotations = array(0, 90, 270);
				break;
			}
		}else{
			$eyeRotations = array(0, 90, 270);
		}

		if (!empty($eye_image)) {
			$eyeball_name = basename($eye_image);
			switch ($eyeball_name) {
				case 'ball1.png':
				$eyeballRotations = array(90, 0, 180);
				break;
				case 'ball2.png':
				$eyeballRotations = array(90, 0, 180);
				break;
				case 'ball3.png':
				$eyeballRotations = array(270, 180, 0);
				break;
				case 'ball6.png':
				$eyeballRotations = array(90, 0, 180);
				break;					
				case 'ball11.png':
				$eyeballRotations = array(90, 0, 180);
				break;	
				case 'ball16.png':
				$eyeballRotations = array(0, 270, 90);
				break;
				case 'ball17.png':
				$eyeballRotations = array(0, 90, 270);
				break;	
				case 'ball18.png':
				$eyeballRotations = array(0, 0, 0);
				break;					
				default:
				$eyeballRotations = array(0, 0, 0);
				break;
			}
		}else{
			$eyeballRotations = array(0, 90, 270);
		}

    	// Define positions and rotation for the eyes (top-left, top-right, bottom-left).
		$eyePositions = array(
			array(
				'x' => 15,
				'y' => 15,
				'rotations' => $eyeRotations[0],
				'rotation' => $eyeballRotations[0],
        		// Top-left
			), 
			array(
				'x' => $qr_width - $scaledEyeFrameWidth - 15,
				'y' => 15,
				'rotations' => $eyeRotations[1],
				'rotation' => $eyeballRotations[1],
        		// Top-right
			), 
			array(
				'x' => 15,
				'y' => $qr_height - $scaledEyeFrameHeight - 15,
				'rotations' => $eyeRotations[2],
				'rotation' => $eyeballRotations[2],
        		// Bottom-left
			), 
		);
		
    	// Overlay the eye frames and eyeballs onto the QR code.
		foreach ( $eyePositions as $position ) {
        	// Rotate the eye frame
			$rotatedEyeFrame = imagerotate($scaledEyeFrame, $position['rotations'], 0);

        	// Get the new dimensions of the rotated frame
			$rotatedEyeFrameWidth = imagesx($rotatedEyeFrame);
			$rotatedEyeFrameHeight = imagesy($rotatedEyeFrame);

        	// Overlay the rotated eye frame onto the QR code
			imagecopy(
				$qr_image_resource,
				$rotatedEyeFrame,
				$position['x'],
				$position['y'],
				0,
				0,
				$rotatedEyeFrameWidth,
				$rotatedEyeFrameHeight
			);

       		// Rotate the eye image
			$rotatedEyeImage = imagerotate($eyeImage, $position['rotation'], 0);

       		// Get the new dimensions of the rotated eyeball
			$rotatedEyeImageWidth = imagesx($rotatedEyeImage);
			$rotatedEyeImageHeight = imagesy($rotatedEyeImage);

       		// Calculate the position for the eyeball
			$eyeBallX = $position['x'] + ( $rotatedEyeFrameWidth - $rotatedEyeImageWidth ) / 2;
			$eyeBallY = $position['y'] + ( $rotatedEyeFrameHeight - $rotatedEyeImageHeight ) / 2;

       		// Overlay the rotated eyeball onto the QR code
			imagecopy(
				$qr_image_resource,
				$rotatedEyeImage,
				$eyeBallX,
				$eyeBallY,
				0,
				0,
				$rotatedEyeImageWidth,
				$rotatedEyeImageHeight
			);

       		// Free up memory
			imagedestroy($rotatedEyeFrame);
			imagedestroy($rotatedEyeImage);
		}
		
   		// Free up memory
		imagedestroy($eyeImage);
		// }

		$frame_image_resource = '';
		if ( ! empty( $frame_image ) ) {
			// Load the background frame image.
			$frame_image_resource = imagecreatefrompng( $frame_image );
		}else{
			$frame_image = CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/default.png';
			$frame_image_resource = imagecreatefrompng( $frame_image );
		}

		// Get the dimensions of the frame image.
		$frame_width  = imagesx( $frame_image_resource );
		$frame_height = imagesy( $frame_image_resource );

		// Calculate the scale factor for the QR code to fit within the frame.
		$qr_scale = min( $frame_width, $frame_height ) * 0.8 / max( $qr_width, $qr_height );
		
		// Calculate the scaled dimensions of the QR code.
		$scaled_qr_width  = $qr_width * $qr_scale;
		$scaled_qr_height = $qr_height * $qr_scale;

		$frame_images   = basename( $frame_image );
		$padding_top    = 0;
		$padding_bottom = 0;

		// Switch-case to set default padding based on frame_image.
		switch ( $frame_images ) {
			case 'balloon-bottom.png':
			$padding_top = -300;
			break;
			case 'balloon-bottom-1.png':
			$padding_top = -300;
			break;
			case 'balloon-top.png':
			$padding_top = 300;
			break;
			case 'balloon-top-2.png':
			$padding_top = 300;
			break;
			case 'banner-bottom.png':
			$padding_top = -300;
			break;
			case 'banner-bottom-3.png':
			$padding_top = -300;
			break;
			case 'banner-top.png':
			$padding_top = 300;
			break;
			case 'banner-top-4.png':
			$padding_top = 300;
			break;
			case 'box-bottom.png':
			$padding_top = -300;
			break;
			case 'box-bottom-5.png':
			$padding_top = -300;
			break;
			case 'box-Top.png':
			$padding_top = 300;
			break;
			case 'box-Top-6.png':
			$padding_top = 300;
			break;
			case 'focus-8-lite.png':
			$padding_top = -350;
			break;
			case 'focus-lite.png':
			$padding_top = -350;
			break;
			case 'default.png':
			$padding_top = 0;
			break;
			default:
			$padding_top = 0;
			break;
		}

		// Calculate the position to center the QR code within the frame.
		$qr_x = ( $frame_width - $scaled_qr_width ) / 2;
		$qr_y = ( $frame_height - $scaled_qr_height - $padding_top - $padding_bottom ) / 2 + $padding_top;

		// Resize the QR code image.
		$resized_qr_image = imagescale( $qr_image_resource, $scaled_qr_width, $scaled_qr_height );

		// Create a new image to hold the merged result (frame with QR code).
		$merged_image_resource = imagecreatetruecolor( $frame_width, $frame_height );
		
		// Merge the frame image onto the new image.
		imagecopy( $merged_image_resource, $frame_image_resource, 0, 0, 0, 0, $frame_width, $frame_height );

		// Merge the resized QR code onto the new image (frame).
		imagecopy( $merged_image_resource, $resized_qr_image, $qr_x, $qr_y, 0, 0, $scaled_qr_width, $scaled_qr_height );
		// }
		
		// Optionally, load and add the logo image.
		if ( ! empty( $logo_url ) && $logo_url !== 'no') {
			$file_extension = pathinfo( $logo_url, PATHINFO_EXTENSION );

			switch ( strtolower( $file_extension ) ) {
				case 'png':
				$logo_image_resource = imagecreatefrompng( $logo_url );
				break;
				case 'jpg':
				case 'jpeg':
				$logo_image_resource = imagecreatefromjpeg( $logo_url );
				break;
				default:
				break;
			}

			// Get the dimensions of the logo image.
			$logo_width  = imagesx( $logo_image_resource );
			$logo_height = imagesy( $logo_image_resource );

			$logo_padding_top    = 300;
			$logo_padding_bottom = 50;
			$frame_images        = basename( $frame_image );

			switch ( $frame_images ) {
				case 'balloon-bottom.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'balloon-bottom-1.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'balloon-top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'balloon-top-2.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'banner-bottom.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 100;
				break;
				case 'banner-bottom-3.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 100;
				break;
				case 'banner-top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'banner-top-4.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'box-bottom.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'box-bottom-5.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'box-Top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'box-Top-6.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'focus-8-lite.png':
				$logo_padding_top    = -350;
				$logo_padding_bottom = 50;
				break;
				case 'focus-lite.png':
				$logo_padding_top    = -350;
				$logo_padding_bottom = 50;
				break;
				case 'default.png':
				$logo_padding_top    = 0;
				$logo_padding_bottom = 0;
				break;
			}

			// Calculate the size and position of the logo relative to the frame with padding.
			$logo_size = min( $frame_width, $frame_height ) / 5;
			$logo_x    = ( $frame_width - $logo_size ) / 2;
			$logo_y    = ( $frame_height - $logo_size - $logo_padding_top - $logo_padding_bottom ) / 2 + $logo_padding_top;

			// Resize the logo image.
			$resized_logo_image = imagescale( $logo_image_resource, $logo_size, $logo_size );

			// Merge the logo onto the new image (frame with QR code).
			imagecopy( $merged_image_resource, $resized_logo_image, $logo_x, $logo_y, 0, 0, $logo_size, $logo_size );

			// Free memory.
			imagedestroy( $logo_image_resource );
			imagedestroy( $resized_logo_image );
		}

		if (!empty($id) && $id !== 0) {
			$existing_imgdata = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $id)); // phpcs:ignore


			// $existing_imgdata = $wpdb->get_row($query);

			if ( ! $existing_imgdata ) {
				die( esc_html__('No data found for the given ID.', 'custom-qrcode-generator') );
			}

			// Extract existing image data and updated timestamp
			$updated_at       = $existing_imgdata->updated_at;
			$created_at       = $existing_imgdata->created_at;

			// Check if $updated_at is not null or empty
			if ( ! empty( $updated_at ) ) {
				try {
        			// Create a DateTime object from the updated timestamp
					$date = new DateTime( $updated_at );
					$month = $date->format( 'm' );
					$year  = $date->format( 'Y' );
				} catch ( Exception $e ) {
        			// Handle invalid date format error
					die( esc_html__('Invalid date format in updated_at field.', 'custom-qrcode-generator') );
				}
			} else {
				$date = new DateTime( $created_at );
				$month = $date->format( 'm' );
				$year  = $date->format( 'Y' );
			}
		}
		
		// Save the final QR code image to a file.
		$filename = 'cqrc-' . ($id ? $id : 1) . '.png';
		
		// $file     = $upload_dir['basedir'] . '/' . $filename;
		if ($merged_image_resource == '') {
			$merged_image_resource = $qr_image_resource;
		}
		
		$test7=imagepng( $merged_image_resource, $filename );
		return $filename;

		// Free memory.
		imagedestroy( $qr_image_resource );
		imagedestroy( $frame_image_resource );
		imagedestroy( $resized_qr_image );
		imagedestroy( $merged_image_resource );
		wp_cache_flush();
	}
	/**
	 * QRCode generation form submission handle.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_generator_form_handle() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'qrcode_generator';
		//$ext_id = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}qrcode_generator ORDER BY id DESC LIMIT 1");
		//$ext_id = $wpdb->get_row($wpdb->prepare("SELECT id FROM {$wpdb->prefix}qrcode_generator ORDER BY id DESC LIMIT 1"));
		
		$site_url = site_url();
		if ( isset( $_POST['qrcode_url'] ) && isset( $_POST['qrcode_name'] ) ) {

			if ( ! isset( $_POST['qr_code_form_data_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qr_code_form_data_nonce'] ) ), 'qr_code_form_data' ) ) {
				return;
			}

			$urls              = isset( $_POST['qrcode_url'] ) ? sanitize_text_field( wp_unslash($_POST['qrcode_url'])) : '';
			$name              = isset( $_POST['qrcode_name'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_name'] ) ) : '';
			$description       = isset( $_POST['qrcode_description'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_description'] ) ) : '';
			$qrid          	   = isset( $_POST['qrid'] ) ? sanitize_text_field( wp_unslash( $_POST['qrid'] ) ) : '';
			$upload_logo_url   = isset( $_POST['upload_logo_url'] ) ? sanitize_text_field( wp_unslash( $_POST['upload_logo_url'] ) ) : '';
			$default_logo      = isset( $_POST['default_logo'] ) ? sanitize_text_field( wp_unslash( $_POST['default_logo'] ) ) : '';
			$default_frame     = isset( $_POST['default_frame'] ) ? sanitize_text_field( wp_unslash( $_POST['default_frame'] ) ) : '';
			$eye_frame_name    = isset( $_POST['eye_frame_name'] ) ? sanitize_text_field( wp_unslash( $_POST['eye_frame_name'] ) ) : '';
			$eye_balls_name    = isset( $_POST['eye_balls_name'] ) ? sanitize_text_field( wp_unslash( $_POST['eye_balls_name'] ) ) : '';
			$template_name     = isset( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash($_POST['template_name'] )) : '';
			$qrcode_level      = isset( $_POST['qrcode_level'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_level'] ) ) : 'QR_ECLEVEL_H';
			$password      = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
			$qr_eye_frame_color= isset( $_POST['qr_eye_frame_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_eye_frame_color'] )) : '';
			$qr_eye_color     = isset( $_POST['qr_eye_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_eye_color'] )) : '';
			$qr_code_color      = isset( $_POST['qr_code_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_code_color'] )) : '';
			
			$framepath = '';

			if('default' == $default_frame ){
				$framepath = '';
			}else if ( '' !== $default_frame ) {
				$framepath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/' . $default_frame . '.png';
			}

			$eye_framepath = '';
			if('default' == $eye_frame_name ){
				$eye_framepath = '';
			}else if ( '' !== $eye_frame_name ) {
				$eye_framepath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/' . $eye_frame_name . '.png';
			}

			$eye_balls_path = '';
			if('default' == $eye_balls_name ){
				$eye_balls_path = '';
			}else if ( '' !== $eye_balls_name ) {
				$eye_balls_path = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/' . $eye_balls_name . '.png';
			}
			$logo_option = isset( $_POST['logo_option'] ) ? sanitize_text_field( wp_unslash( $_POST['logo_option'] ) ) : '';
			
			$u_id = get_current_user_id();
			$uploaded_logo = '';
			if (!empty($qrid)) {
				$qr_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $qrid ), ARRAY_A ); // phpcs:ignore
				if ( isset( $qr_data ) ) {
					$uploaded_logo = $qr_data['default_logo_name'];
				}
			}
			
			$logopath = '';
			if ( 'default' === $logo_option ) {
				if($default_logo == 'default'){
					$logopath = '';
				}elseif ( '' !== $default_logo ) {
					$logopath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/logos/' . $default_logo . '.png';
				}
			} elseif ( 'upload' === $logo_option ) {
				if ( isset( $upload_logo_url ) && '' !== $upload_logo_url ) {
					$logopath = $upload_logo_url;
				} else {
					$logopath = $uploaded_logo;
				}
			}

			$data = array(
				'user_id'            => $u_id,
				'name'               => $name,
				'description' 		 => $description,
				'upload_logo'        => 'upload' === $logo_option ? 'upload' : 'default',
				'logo_type'          => 'PNG',
				'url'                => $urls,
				// 'total_scans'        => '',
				'template_name'      => $template_name,
				'default_logo_name'  => ($logopath ? $logopath: 'default'),
				'frame_name'         => $default_frame,
				'eye_frame_name'     => $eye_frame_name,
				'eye_balls_name'     => $eye_balls_name,
				'qr_eye_color'       => $qr_eye_color,
				'qr_eye_frame_color' => $qr_eye_frame_color,
				'qr_code_color'      => $qr_code_color,
				'qrcode_level'       => $qrcode_level,
				'status'             => 'publish',
				'token'              => '',
				'password'           => $password,
			);

			if ( isset( $qrid ) ) {
				// Check if $qrid exists in the database.
				$existing_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $qrid ) ); // phpcs:ignore

				if ( $existing_data ) {
					// Retrieve the existing QR code URL from the database.
					$existing_qr_code_url = $existing_data->qr_code;

					// Fetch posts where the guid matches the existing QR code URL.
					// phpcs:disable
						$posts_to_delete = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT ID FROM $wpdb->posts WHERE guid = %s",
								$existing_qr_code_url
							)
						);
					// phpcs:enable
					// Delete posts if any are found.
					foreach ( $posts_to_delete as $post ) {
					// 'true' to force delete.
						wp_delete_post( $post->ID, true );
					}
				// }
				// if ( $existing_data ) {
					// Update existing record.
					// phpcs:disable
					$wpdb->update(
						$table_name,
						$data,
						array( 'id' => $qrid ),
						array(
							'%d', // user_id.
							'%s', // name.
							'%s', // description.
							'%s', // upload_logo.
							'%s', // logo_type.
							'%s', // url.
							'%s', // total_scans.
							'%s', // template_name.
							'%s', // default_logo_name.
							'%s', // frame_name.
							'%s', // eye_frame_name.
							'%s', // eye_balls_name.
							'%s', // qr_eye_color.
							'%s', // qr_eye_frame_color.
							'%s', // qr_code_color.
							'%s', // qrcode_level.
							'%s', // status.
							'%s', // token.
							'%s', // password.
						),
						array( '%d' )
					);
					//phpcs:enable
					$identifier_with_suffix = $qrid . 'FTA';
					$qrcode_scan_nonce = wp_create_nonce('qrcode_scan_nonce');
					$url = $site_url . '/qrcode_scan?url=' . bin2hex($urls) . '&qrid=' .$identifier_with_suffix.'&qrcode_wpnonce='.$qrcode_scan_nonce;
					
					$qr_code_url = $this->cqrc_generator_create_qr_code( $url, $qrid, $logopath, $framepath, $eye_framepath, $eye_balls_path, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password);

					if ( is_wp_error( $qr_code_url ) ) {
						echo 'Error generating QR code: ' . esc_url( $qr_code_url );
						return;
					}

					// Update the record with the QR code URL.
					// phpcs:disable
					$wpdb->update(
						$table_name,
						array( 'qr_code' => $qr_code_url ),
						array( 'id' => $qrid ),
						array( '%s' ),
						array( '%d' )
					);
					//phpcs:enable

					wp_safe_redirect( admin_url( 'admin.php?page=custom-qrcode-generator' ) );
					exit;
				}
			}

			// If $qrid is empty or not found in the database, insert new record.
			$data['total_scans'] = '0'; 
			$new_data = $wpdb->insert( $table_name, $data ); // phpcs:ignore

			if ( false === $new_data ) {
				echo 'Database insertion error: ' . esc_html( $wpdb->last_error );
			} else {
				// $url = $site_url.'/qrcode_scan?url='.bin2hex($urls).'&qrid='.dechex($ext_id->id+1);
				$lastid      = $wpdb->insert_id;
				$identifier_with_suffix = $lastid . 'FTA';
				$qrcode_scan_nonce = wp_create_nonce('qrcode_scan_nonce');
				$url = $site_url . '/qrcode_scan?url=' . bin2hex($urls) . '&qrid=' .$identifier_with_suffix. '&qrcode_wpnonce=' .$qrcode_scan_nonce;
				$qr_code_url = $this->cqrc_generator_create_qr_code( $url, $lastid, $logopath, $framepath, $eye_framepath, $eye_balls_path, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password);

				if ( is_wp_error( $qr_code_url ) ) {
					echo 'Error generating QR code: ' . esc_url( $qr_code_url );
					return;
				}

				// Update the record with the QR code URL.
				// phpcs:disable
				$wpdb->update(
					$table_name,
					array( 'qr_code' => $qr_code_url ),
					array( 'id' => $lastid ),
					array( '%s' ),
					array( '%d' )
				);
				// phpcs:enable
				wp_safe_redirect( admin_url( 'admin.php?page=custom-qrcode-generator' ) );
				exit;
			}
		}
	}
	
	/**
	 * QRCode generate function PHPQRCODE.
	 *
	 * @param mixed $url returns URL of QR.
	 * @param int   $id returns id.
	 * @param mixed $logo_url returns logo url.
	 * @param mixed $frame_image returns frame image.
	 * @since 1.0.0
	 */
	public function cqrc_generator_create_qr_code( $url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password ) {
		global $wpdb;
		$merged_image_resource = '';
		$table_name = $wpdb->prefix . 'qrcode_generator';

		// Include PHP QR Code library.
		include_once CQRCGEN_INCLUDES_DIR . '/phpqrcode/qrlib.php';

		// QR Code Black & White Combination Fixes.
		switch ( $qr_code_color ) {
			case '#ffffff':
			$qr_code_bg_color = 0;
			$qr_eye_color     = '#ffffff';
			break;
			case '#000000':
			$qr_code_bg_color = 16777215;
			break;
			default:
			$qr_code_bg_color = 16777215;
		}

		$qr_eye_frame_color = $this->cqrc_hex_to_rgb( $qr_eye_frame_color );
		
		// Convert hex color codes to integers.
		if ($qr_code_color !== '') {
			$qr_code_color = hexdec( ltrim( $qr_code_color, '#' ) );
		}

		// Usage.
		$qr_eye_rgb = $this->cqrc_hex_to_rgb( $qr_eye_color );

		// QR Code Level Constants Convertion.
		switch ( $qrcode_level ) {
			case 'QR_ECLEVEL_Q':
			$qrcode_level = QR_ECLEVEL_Q;
			break;
			case 'QR_ECLEVEL_H':
			$qrcode_level = QR_ECLEVEL_H;
			break;
			case 'QR_ECLEVEL_M':
			$qrcode_level = QR_ECLEVEL_M;
			break;
			default:
			$qrcode_level = QR_ECLEVEL_H;
		}
		
		if ($password !== '' || !empty($password)) {
			$token = bin2hex(random_bytes(16));
			// phpcs:disable
			$wpdb->update(
				$table_name,
				array( 'token' => $token ),
				array( 'id' => $id ),
				array( '%s' ),
				array( '%d' )
			);
			// phpcs:enable
			$data = $url.'&token='.$token;
		}else{
			$data = $url;
			// phpcs:disable
			$wpdb->update(
				$table_name,
				array( 'token' => '' ),
				array( 'password' => '' ),
				array( 'id' => $id ),
				array( '%s' ),
				array( '%s' ),
				array( '%d' )
			);
			// phpcs:enable
		}

		// Generate QR code and save it to a temporary file.
		ob_start();
		$my_qr = QRcode::png( $url, null, $qrcode_level, 15, 1, false, $qr_code_bg_color, $qr_code_color );
		
		$qr_image = ob_get_clean();
		
		if ($qr_image === false) {
			die( esc_html__('Failed to get QR code image data.', 'custom-qrcode-generator') );
		}

		$qr_image_resource = imagecreatefromstring($qr_image);
		
		if ($qr_image_resource === false) {
			die( esc_html__('Failed to create image from string.', 'custom-qrcode-generator') );
		}


		$qr_width = imagesx($qr_image_resource);
		$qr_height = imagesy($qr_image_resource);

		if ($qr_width === false || $qr_height === false) {
			die( esc_html__('Failed to get image dimensions.', 'custom-qrcode-generator') );
		}

		$eyeFrame = '';
		if ( ! empty( $eye_frame_image ) ) {
		// Load the custom frame for the eyes.
			$eyeFrame = imagecreatefrompng( $eye_frame_image );
		}else{
			$eye_frame_image = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/frame0.png';
			$eyeFrame = imagecreatefrompng( $eye_frame_image );
		}

		// Get dimensions of the eye frame.
		$eyeFrameWidth  = imagesx( $eyeFrame );
		$eyeFrameHeight = imagesy( $eyeFrame );

		// Define the desired scale factor for the eye frames (e.g., 1.5 for 150% size).
		$scaleFactor = 2.1;

		// Calculate the new dimensions of the eye frame.
		$scaledEyeFrameWidth  = $eyeFrameWidth * $scaleFactor;
		$scaledEyeFrameHeight = $eyeFrameHeight * $scaleFactor;

		// Create a new true color image for the scaled eye frame.
		$scaledEyeFrame = imagecreatetruecolor( $scaledEyeFrameWidth, $scaledEyeFrameHeight );

		// Enable transparency for the new image.
		imagealphablending( $scaledEyeFrame, false );
		imagesavealpha( $scaledEyeFrame, true );

		// Resize the eye frame to the new dimensions.
		imagecopyresampled(
			$scaledEyeFrame,
			$eyeFrame,
			0,
			0,
			0,
			0,
			$scaledEyeFrameWidth,
			$scaledEyeFrameHeight,
			$eyeFrameWidth,
			$eyeFrameHeight
		);

		// Apply color to the eye frame.
		imagefilter( $scaledEyeFrame, IMG_FILTER_COLORIZE, $qr_eye_frame_color['r'], $qr_eye_frame_color['g'], $qr_eye_frame_color['b'], 0 );
		// }

		$eyeImage = '';
		if ( ! empty( $eye_image ) ) {
    		// Load the eyeball image.
			$eyeImage = imagecreatefrompng( $eye_image );
		}else{
			$eye_image = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/ball0.png';
			$eyeImage = imagecreatefrompng( $eye_image );

		}

    	// Apply color to the eyeball.
		imagefilter( $eyeImage, IMG_FILTER_COLORIZE, $qr_eye_rgb['r'], $qr_eye_rgb['g'], $qr_eye_rgb['b'], 0 );

    	// Get dimensions of the eyeball image.
		$eyeImageWidth  = imagesx( $eyeImage );
		$eyeImageHeight = imagesy( $eyeImage );

		// Define the rotation values for each eye frame image name
		if (!empty($eye_frame_image)) {
			$eye_name = basename($eye_frame_image);

			switch ($eye_name) {
				case 'frame1.png':
				$eyeRotations = array(90, 0, 180);
				break;
				case 'frame2.png':
				$eyeRotations = array(90, 0, 0);
				break;
				case 'frame3.png':
				$eyeRotations = array(270, 180, 0);
				break;
				case 'frame5.png':
				$eyeRotations = array(90, 0, 180);
				break;					
				case 'frame6.png':
				$eyeRotations = array(0, 90, 270);
				break;					
				case 'frame14.png':
				$eyeRotations = array(0, 270, 90);
				break;
				default:
				$eyeRotations = array(0, 90, 270);
				break;
			}
		}else{
			$eyeRotations = array(0, 90, 270);
		}

		if (!empty($eye_image)) {
			$eyeball_name = basename($eye_image);
			switch ($eyeball_name) {
				case 'ball1.png':
				$eyeballRotations = array(90, 0, 180);
				break;
				case 'ball2.png':
				$eyeballRotations = array(90, 0, 180);
				break;
				case 'ball3.png':
				$eyeballRotations = array(270, 180, 0);
				break;
				case 'ball6.png':
				$eyeballRotations = array(90, 0, 180);
				break;					
				case 'ball11.png':
				$eyeballRotations = array(90, 0, 180);
				break;	
				case 'ball16.png':
				$eyeballRotations = array(0, 270, 90);
				break;
				case 'ball17.png':
				$eyeballRotations = array(0, 90, 270);
				break;	
				case 'ball18.png':
				$eyeballRotations = array(0, 0, 0);
				break;					
				default:
				$eyeballRotations = array(0, 0, 0);
				break;
			}
		}else{
			$eyeballRotations = array(0, 90, 270);
		}

    	// Define positions and rotation for the eyes (top-left, top-right, bottom-left).
		$eyePositions = array(
			array(
				'x' => 15,
				'y' => 15,
				'rotations' => $eyeRotations[0],
				'rotation' => $eyeballRotations[0],
        		// Top-left
			), 
			array(
				'x' => $qr_width - $scaledEyeFrameWidth - 15,
				'y' => 15,
				'rotations' => $eyeRotations[1],
				'rotation' => $eyeballRotations[1],
        		// Top-right
			), 
			array(
				'x' => 15,
				'y' => $qr_height - $scaledEyeFrameHeight - 15,
				'rotations' => $eyeRotations[2],
				'rotation' => $eyeballRotations[2],
        		// Bottom-left
			), 
		);

    	// Overlay the eye frames and eyeballs onto the QR code.
		foreach ( $eyePositions as $position ) {
        	// Rotate the eye frame
			$rotatedEyeFrame = imagerotate($scaledEyeFrame, $position['rotations'], 0);

        	// Get the new dimensions of the rotated frame
			$rotatedEyeFrameWidth = imagesx($rotatedEyeFrame);
			$rotatedEyeFrameHeight = imagesy($rotatedEyeFrame);

        	// Overlay the rotated eye frame onto the QR code
			imagecopy(
				$qr_image_resource,
				$rotatedEyeFrame,
				$position['x'],
				$position['y'],
				0,
				0,
				$rotatedEyeFrameWidth,
				$rotatedEyeFrameHeight
			);

       		// Rotate the eye image
			$rotatedEyeImage = imagerotate($eyeImage, $position['rotation'], 0);

       		// Get the new dimensions of the rotated eyeball
			$rotatedEyeImageWidth = imagesx($rotatedEyeImage);
			$rotatedEyeImageHeight = imagesy($rotatedEyeImage);

       		// Calculate the position for the eyeball
			$eyeBallX = $position['x'] + ( $rotatedEyeFrameWidth - $rotatedEyeImageWidth ) / 2;
			$eyeBallY = $position['y'] + ( $rotatedEyeFrameHeight - $rotatedEyeImageHeight ) / 2;

       		// Overlay the rotated eyeball onto the QR code
			imagecopy(
				$qr_image_resource,
				$rotatedEyeImage,
				$eyeBallX,
				$eyeBallY,
				0,
				0,
				$rotatedEyeImageWidth,
				$rotatedEyeImageHeight
			);

       		// Free up memory
			imagedestroy($rotatedEyeFrame);
			imagedestroy($rotatedEyeImage);
		}

   		// Free up memory
		imagedestroy($eyeImage);
		// }

		$frame_image_resource = '';
		if ( ! empty( $frame_image ) ) {
			// Load the background frame image.
			$frame_image_resource = imagecreatefrompng( $frame_image );
		}else{
			$frame_image = CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/default.png';
			$frame_image_resource = imagecreatefrompng( $frame_image );
		}

		// Get the dimensions of the frame image.
		$frame_width  = imagesx( $frame_image_resource );
		$frame_height = imagesy( $frame_image_resource );

		// Calculate the scale factor for the QR code to fit within the frame.
		$qr_scale = min( $frame_width, $frame_height ) * 0.8 / max( $qr_width, $qr_height );

		// Calculate the scaled dimensions of the QR code.
		$scaled_qr_width  = $qr_width * $qr_scale;
		$scaled_qr_height = $qr_height * $qr_scale;

		$frame_images   = basename( $frame_image );
		$padding_top    = 0;
		$padding_bottom = 0;

		// Switch-case to set default padding based on frame_image.
		switch ( $frame_images ) {
			case 'balloon-bottom.png':
			$padding_top = -300;
			break;
			case 'balloon-bottom-1.png':
			$padding_top = -300;
			break;
			case 'balloon-top.png':
			$padding_top = 300;
			break;
			case 'balloon-top-2.png':
			$padding_top = 300;
			break;
			case 'banner-bottom.png':
			$padding_top = -300;
			break;
			case 'banner-bottom-3.png':
			$padding_top = -300;
			break;
			case 'banner-top.png':
			$padding_top = 300;
			break;
			case 'banner-top-4.png':
			$padding_top = 300;
			break;
			case 'box-bottom.png':
			$padding_top = -300;
			break;
			case 'box-bottom-5.png':
			$padding_top = -300;
			break;
			case 'box-Top.png':
			$padding_top = 300;
			break;
			case 'box-Top-6.png':
			$padding_top = 300;
			break;
			case 'focus-8-lite.png':
			$padding_top = -350;
			break;
			case 'focus-lite.png':
			$padding_top = -350;
			break;
			case 'default.png':
			$padding_top = 0;
			break;
			default:
			$padding_top = 0;
			break;
		}

		// Calculate the position to center the QR code within the frame.
		$qr_x = ( $frame_width - $scaled_qr_width ) / 2;
		$qr_y = ( $frame_height - $scaled_qr_height - $padding_top - $padding_bottom ) / 2 + $padding_top;

		// Resize the QR code image.
		$resized_qr_image = imagescale( $qr_image_resource, $scaled_qr_width, $scaled_qr_height );

		// Create a new image to hold the merged result (frame with QR code).
		$merged_image_resource = imagecreatetruecolor( $frame_width, $frame_height );

		// Merge the frame image onto the new image.
		imagecopy( $merged_image_resource, $frame_image_resource, 0, 0, 0, 0, $frame_width, $frame_height );

		// Merge the resized QR code onto the new image (frame).
		imagecopy( $merged_image_resource, $resized_qr_image, $qr_x, $qr_y, 0, 0, $scaled_qr_width, $scaled_qr_height );
		// }
		
		// Optionally, load and add the logo image.
		if ( ! empty( $logo_url ) ) {
			$file_extension = pathinfo( $logo_url, PATHINFO_EXTENSION );
			switch ( strtolower( $file_extension ) ) {
				case 'png':
				$logo_image_resource = imagecreatefrompng( $logo_url );
				break;
				case 'jpg':
				case 'jpeg':
				$logo_image_resource = imagecreatefromjpeg( $logo_url );
				break;
				default:
				break;
			}

			// Get the dimensions of the logo image.
			$logo_width  = imagesx( $logo_image_resource );
			$logo_height = imagesy( $logo_image_resource );

			$logo_padding_top    = 300;
			$logo_padding_bottom = 50;
			$frame_images        = basename( $frame_image );

			switch ( $frame_images ) {
				case 'balloon-bottom.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'balloon-bottom-1.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'balloon-top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'balloon-top-2.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'banner-bottom.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 100;
				break;
				case 'banner-bottom-3.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 100;
				break;
				case 'banner-top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'banner-top-4.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'box-bottom.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'box-bottom-5.png':
				$logo_padding_top    = -200;
				$logo_padding_bottom = 50;
				break;
				case 'box-Top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'box-Top-6.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'focus-8-lite.png':
				$logo_padding_top    = -350;
				$logo_padding_bottom = 50;
				break;
				case 'focus-lite.png':
				$logo_padding_top    = -350;
				$logo_padding_bottom = 50;
				break;
				case 'default.png':
				$logo_padding_top    = 0;
				$logo_padding_bottom = 0;
				break;
			}

			// Calculate the size and position of the logo relative to the frame with padding.
			$logo_size = min( $frame_width, $frame_height ) / 5;
			$logo_x    = ( $frame_width - $logo_size ) / 2;
			$logo_y    = ( $frame_height - $logo_size - $logo_padding_top - $logo_padding_bottom ) / 2 + $logo_padding_top;

			// Resize the logo image.
			$resized_logo_image = imagescale( $logo_image_resource, $logo_size, $logo_size );

			// Merge the logo onto the new image (frame with QR code).
			imagecopy( $merged_image_resource, $resized_logo_image, $logo_x, $logo_y, 0, 0, $logo_size, $logo_size );

			// Free memory.
			imagedestroy( $logo_image_resource );
			imagedestroy( $resized_logo_image );
		}

		$existing_imgdata = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $id ) );// phpcs:ignore
		if ( ! $existing_imgdata ) {
    		// Handle case where no data is returned
			die( esc_html__('No data found for the given ID.', 'custom-qrcode-generator' ) );
		}

		// Extract existing image data and updated timestamp
		$old_path_file    = basename( $existing_imgdata->qr_code );
		$updated_at       = $existing_imgdata->updated_at;
		$created_at       = $existing_imgdata->created_at;

		// Check if $updated_at is not null or empty
		if ( ! empty( $updated_at ) ) {
			try {
        		// Create a DateTime object from the updated timestamp
				$date = new DateTime( $updated_at );
				$month = $date->format( 'm' );
				$year  = $date->format( 'Y' );
			} catch ( Exception $e ) {
        		// Handle invalid date format error
				die( esc_html__('Invalid date format in updated_at field.', 'custom-qrcode-generator') );
			}
		} else {
			$date = new DateTime( $created_at );
			$month = $date->format( 'm' );
			$year  = $date->format( 'Y' );
		}
		$upload_dir       = wp_upload_dir();
		$old_filename     = 'cqrc-' . $id . '.png';

		if (!empty($old_path_file )) {
			$old_file_path    = $upload_dir['basedir'] . '/' . $year . '/' . $month . '/' . $old_path_file;

				// Remove old image if it exists.
			if ( file_exists( $old_file_path ) ) {
				// unlink( $old_file_path );
				wp_delete_file( $old_file_path );
			}
		}

		// Save the final QR code image to a file.
		$filename = 'cqrc-' . $id . '.png';
		$file     = $upload_dir['basedir'] . '/' . $filename;
		if ($merged_image_resource == '') {
			$merged_image_resource = $qr_image_resource;
		}

		imagepng( $merged_image_resource, $file );

		// Apply filter to prevent intermediate image sizes.
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'cqrc_disable_image_sizes' ) );

		// Prepare the file array for wp_handle_sideload.
		$file_array = array(
			'name'     => $filename,
			'tmp_name' => $file,
		);

		// Include required files if not already included.
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		// Handle sideload.
		$attachment_id = media_handle_sideload( $file_array, 0 );

		// Remove the filter after upload.
		remove_filter( 'intermediate_image_sizes_advanced', array( $this, 'cqrc_disable_image_sizes' ) );

		// Check for upload errors.
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id->get_error_message();
		} else {
			// Get the URL of the uploaded image.
			$image_url = wp_get_attachment_url( $attachment_id );
			return $image_url;
		}

		// Free memory.
		imagedestroy( $qr_image_resource );
		imagedestroy( $frame_image_resource );
		imagedestroy( $resized_qr_image );
		imagedestroy( $merged_image_resource );
		wp_cache_flush();
	}

	/**
	 * Disable intermediate image sizes.
	 *
	 * @param array $sizes Array of intermediate image sizes.
	 * @return array Modified array of image sizes.
	 */
	public function cqrc_disable_image_sizes( $sizes ) {
		return array();
	}

	/**
	 * cqrc_Hex_to_rgb
	 *
	 * @param  mixed $hex color.
	 */
	public function cqrc_hex_to_rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) === 6 ) {
			list($r, $g, $b) = sscanf( $hex, '%02x%02x%02x' );
		} elseif ( strlen( $hex ) === 3 ) {
			list($r, $g, $b) = sscanf( $hex, '%1x%1x%1x' );
			$r               = $r * 0x11;
			$g               = $g * 0x11;
			$b               = $b * 0x11;
		} else {
			return false;
		}
		return array(
			'r' => $r,
			'g' => $g,
			'b' => $b,
		);
	}

	/**
	 * QRCode Download Option handle.
	 *
	 * @param mixed $url returns URL of QR.
	 * @param int   $id returns id.
	 * @param mixed $logo_url returns logo url.
	 * @param mixed $frame_image returns frame image.
	 * @since 1.0.0
	 */
	public function cqrc_handle_qr_code_delete_action() {
		if ( isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) ) {

			// Check nonce
			if (!isset($_REQUEST['_qr_code_nonce_action']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_qr_code_nonce_action'])), 'qr_code_nonce_action')) {
				wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
			}
			
    		// Proceed with deletion logic.
			$id = intval($_GET['id']);
			global $wpdb;
			$table_name = $wpdb->prefix . 'qrcode_generator';
			$insights_table = $wpdb->prefix . 'qrcode_insights';

    		// Step 1: Retrieve the QR Code Data.
			$qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT qr_code, id AS qrid FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $id ) ); // phpcs:ignore

			$qr_code_rows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $id )); // phpcs:ignore
			if ( ! $qr_code_row ) {
       		// QR code record with the given ID doesn't exist.
				return;
			}

			if ( ! $qr_code_rows ) {
        	// QR code record with the given ID doesn't exist.
				return;
			}

			$qr_code = $qr_code_row->qr_code;
			$qrid = $qr_code_rows->id;

    		// Step 2: Find Matching Media Posts.
			$media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment'", $qr_code ) ); // phpcs:ignore

    		// Step 3: Delete All Matching Media Posts.
			if ( $media_posts ) {
				foreach ( $media_posts as $media_post ) {
            	// true for force delete, false for trash.
					wp_delete_post( $media_post->ID, true );
				}
			}

    		// Step 4: Delete Records from qrcode_insights where qrid matches
			$wpdb->delete( $insights_table, array( 'qrid' => $qrid ), array( '%d' ) );// phpcs:ignore

   			// Step 5: Delete the QR Code Record.
			$wpdb->delete( $table_name, array( 'ID' => $id ), array( '%d' ) );// phpcs:ignore
			$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
			// Redirect after deletion
			wp_redirect(admin_url('admin.php?page=' . $page));
			exit;
		}
	}
	public function cqrc_handle_qr_code_download() {
		if ( isset($_GET['action']) && $_GET['action'] === 'download_qr' ) {
			
			if(!isset($_GET['custom'])) {
				// Verify the nonce before processing further
				if (!isset($_GET['download_qr_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['download_qr_nonce'])), 'download_qr_nonce')) {
					wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
				}
			}
			
			$id = isset($_GET['id']) ? absint($_GET['id']) : 0;
			$type = isset($_GET['type']) ? sanitize_text_field(wp_unslash($_GET['type'])) : '';


        	// Validate file type
			if ( ! in_array( $type, array( 'png', 'jpg', 'pdf' ), true ) ) {
				wp_die( esc_html__('Invalid file type.', 'custom-qrcode-generator' ) );
			}
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'qrcode_generator';

        	// Retrieve QR Code URL
			$qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $id ) );// phpcs:ignore

			if ( ! $qr_code_row ) {
				wp_die( esc_html__('QR code not found.', 'custom-qrcode-generator') );
			}

			$qr_code_url = esc_url($qr_code_row->qr_code);
			
			$qr_code_name = esc_html($qr_code_row->name);
			$file_extension = esc_html($type);

			// Generate PDF using FPDF
			if ( $type === 'pdf' ) {

				if ( !class_exists( '\\Dompdf\\Dompdf' ) ) {
					require_once( CQRCGEN_INCLUDES_DIR . '/vendor/autoload.php' );
				}

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
			die();

		}
	}
}