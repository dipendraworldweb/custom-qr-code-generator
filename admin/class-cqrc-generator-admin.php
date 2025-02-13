<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://www.worldwebtechnology.com/
 * @since      1.0.0
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/admin
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

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
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_enqueue_styles($hook) {
		wp_enqueue_style( $this->plugin_name, CQRCGEN_ADMIN_URL . '/assets/css/cqrc-generator-admin.css', array(), $this->version, 'all' );
		if ( 'qr-code_page_custom-qrcode-generate-form' == $hook ) {
			wp_enqueue_style( $this->plugin_name . '-font-awesome', CQRCGEN_ADMIN_URL . '/assets/css/cqrc-font-awesome.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_enqueue_scripts($hook) {
		if ( 'toplevel_page_custom-qr-code-generator' == $hook || 'qr-code_page_custom-qrcode-generate-form' == $hook || 'qr-code_page_custom-qrcode-export' == $hook || 'qr-code_page_custom-qrcode-about' == $hook || 'qr-code_page_custom-qrcode-users' == $hook || 'qr-code_page_custom-qrcode-import' == $hook || 'qr-code_page_custom-qrcode-default-setting' == $hook) {

			wp_enqueue_style( 'wp-list-table' );
			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_script( $this->plugin_name . '-admin', CQRCGEN_ADMIN_URL . '/assets/js/cqrc-generator-admin.js', array( 'jquery' ), time(), false );
			
			// Localize the script with data.
			wp_localize_script(
				$this->plugin_name . '-admin',
				'wwtQrCodeGenerator',
				array(
					'pluginLogoImagePath'     => CQRCGEN_ADMIN_URL . '/assets/qrcode/logos/',
					'pluginFrameImagePath'    => CQRCGEN_ADMIN_URL . '/assets/qrcode/frames/',
					'pluginTemplateImagePath' => CQRCGEN_ADMIN_URL . '/assets/qrcode/qr-templates/',
					'pluginEyeFrameImagePath' => CQRCGEN_ADMIN_URL . '/assets/qrcode/eye-frames/',
					'pluginEyeBallsImagePath' => CQRCGEN_ADMIN_URL . '/assets/qrcode/eye-balls/',
					'ajax_url'                => admin_url( 'admin-ajax.php' ),
					'nonce'                   => wp_create_nonce( 'qr_code_nonce' ),
					'downloadUrl'             => esc_url( home_url( '/download-qr/' ) )
				)
			);
		}
	}

	/**
	 * Add Top Level Menu Page
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function cqrc_admin_menu() {
    	// Main menu page.
		add_menu_page(
			esc_html__( 'QR Code', 'custom-qr-code-generator' ), 
			esc_html__( 'QR Code', 'custom-qr-code-generator' ), 
			CQRCGEN_LEVEL, 
			'custom-qr-code-generator', 
			array( $this, 'cqrc_generator_index_menu_page' ),
			'dashicons-admin-site'
		);

    	// Submenu page for "Add New QR".
		add_submenu_page(
			'custom-qr-code-generator',
			esc_html__( 'Add New QR', 'custom-qr-code-generator' ),
			esc_html__( 'Add New QR', 'custom-qr-code-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-generate-form',
			array( $this, 'cqrc_form_page' )
		);

    	// Submenu page for "QR Code Records".
		add_submenu_page(
			'custom-qr-code-generator',
			esc_html__( 'QR Code Records', 'custom-qr-code-generator' ),
			esc_html__( 'QR Code Records', 'custom-qr-code-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-users-records',
			array( $this, 'cqrc_qrcode_users_page' )
		);

		// Submenu page for "Export QR Codes".
		add_submenu_page(
			'custom-qr-code-generator',
			esc_html__( 'QR Code Export','custom-qr-code-generator' ),
			esc_html__( 'QR Code Export','custom-qr-code-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-export',
			array( $this, 'cqrc_export_qr_codes')
		);

		// Submenu page for "Import QR Codes".
		add_submenu_page(
			'custom-qr-code-generator',
			esc_html__( 'QR Code Import','custom-qr-code-generator' ),
			esc_html__( 'QR Code Import','custom-qr-code-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-import',
			array( $this, 'cqrc_import_qr_codes')
		);

    	// Submenu page for "About Plugin".
		add_submenu_page(
			'custom-qr-code-generator',
			esc_html__( 'About Plugin', 'custom-qr-code-generator' ),
			esc_html__( 'About Plugin', 'custom-qr-code-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-about',
			array( $this, 'cqrc_about_page' )
		);
	}

	/**
	 * Listing Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function cqrc_generator_index_menu_page() {
		require_once CQRCGEN_ADMIN_DIR . '/class-cqrc-index-page.php';
	}
	
	/**
	 * About Numbers of users QR Code Records Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function cqrc_qrcode_users_page() {
		require_once CQRCGEN_ADMIN_DIR . '/class-cqrc-users-index-page.php';
	}

	/**
	 * About Plugin Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function cqrc_about_page() {
		require_once CQRCGEN_ADMIN_DIR . '/partials/cqrc-about-us.php';
	}
	
	/**
	 * Export the QR code table record
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function cqrc_export_qr_codes() {
		$required_columns = ['id', 'qr_code', 'url', 'name'];
		$columns          = ['id', 'url', 'name', 'description', 'template_name', 'download', 'download_content', 'upload_logo', 'default_logo_name', 'qr_code_color', 'frame_name', 'eye_frame_name', 'qr_eye_frame_color', 'eye_balls_name', 'qr_eye_color', 'qrcode_level', 'created_at', 'updated_at', 'qr_code', 'user_id', 'total_scans'];
		if ( ! empty( $_POST['export'] ) ) {

			if ( empty( $_REQUEST['csv_export_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['csv_export_nonce_field'] ) ), 'csv_export_action' ) ) {
				wp_die( esc_html__( 'Security check failed.', 'custom-qr-code-generator' ) );
			}

			global $wpdb;
			$table_name = esc_sql( QRCODE_GENERATOR_TABLE );
			$query      = $wpdb->prepare( "SELECT `id`, `user_id`, `qr_code`, `total_scans`, `url`, `name`, `description`, `template_name`, `download`, `download_content`, `upload_logo`,`default_logo_name`, `qr_code_color`, `frame_name`, `eye_frame_name`, `qr_eye_frame_color`, `eye_balls_name`, `qr_eye_color`, `qrcode_level`, `created_at`, `updated_at` FROM {$table_name} ORDER BY %s", 'ASC' );// phpcs:ignore 
			$data       = $wpdb->get_results( $query, ARRAY_A );// phpcs:ignore 
        	// Create the export directory if it doesn't exist
			$upload_dir = plugin_dir_path(__FILE__) . 'exports/';
			if( ! is_dir( $upload_dir ) ) {
				wp_mkdir_p( $upload_dir );
			}

			$file_name = 'qr_codes_export_' . time() . '.csv';
			$file_path = $upload_dir . $file_name;

        	// Open file for writing
			$output    = fopen( $file_path, 'w' ); // phpcs:ignore
			if ( ! empty( $_POST['columns'] ) ) {
				$selected_columns = array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['columns'] ) );
			} else {
				$selected_columns = [];
			}

        	// Ensure required columns are always selected
			$selected_columns = array_unique( array_merge( $required_columns, $selected_columns ) );

        	// Filter the columns order to include only the selected columns
			$selected_columns_sorted = array_filter( $columns, function( $column ) use ( $selected_columns ) {
				return in_array( $column, $selected_columns );
			});

        	// Write the header row to the CSV
			fputcsv( $output, $selected_columns_sorted );

        	// Write the data rows to the CSV
			foreach ( $data as $row ) {
				$filtered_row = [];
				foreach ( $selected_columns_sorted as $column ) {
					if ( 'description' === $column && ! empty( $row[$column] ) ) {
						$row[$column] = maybe_unserialize($row[$column]);
					}
					else if( 'download_content' === $column && ! empty( $row[$column] ) ) {
						$download_content = json_decode( $row[$column], true );
						if( ! empty( $download_content ) && is_array( $download_content ) ) {
							$filtered_download_content = array_filter( $download_content );
							$row[$column]              = implode( ',', $filtered_download_content );
						}
					}
					
					$filtered_row[] = !empty( $row[$column] ) ? $row[$column] : '';
				}
				fputcsv( $output, $filtered_row );
			}
			fclose( $output ); // phpcs:ignore

			$export_file_url = CQRCGEN_ADMIN_URL . '/exports/' . $file_name;
			echo '<div id="qrcode-loader" style="display: none;"></div>';
			echo '<h2>' . esc_html__( 'Export QR Codes', 'custom-qr-code-generator' ) . '</h2>';
			echo '<p>' . esc_html__( 'Your CSV file has been created. Click the link below to download:', 'custom-qr-code-generator' ) . '</p>';
			echo '<a href="' . esc_url( $export_file_url ) . '" class="button button-primary">' . esc_html__( 'Download CSV', 'custom-qr-code-generator' ) . '</a>';
			return;
		}
		?>
		<h2><?php esc_html_e( 'Export QR Codes Table Records', 'custom-qr-code-generator' ); ?></h2>
		<form method="post" action="#" enctype="multipart/form-data" id="cqrc-export-process-form">
			<?php wp_nonce_field( 'csv_export_action', 'csv_export_nonce_field' ); ?>
			<h3>
				<?php esc_html_e( 'Select Columns to Export:', 'custom-qr-code-generator' ); ?>
			</h3>
			<p style="color: #f00; font-size: 14px; font-style: italic; font-weight:400;">
				<?php esc_html_e( 'Note: The following fields are required for export and cannot be unchecked: ID, User Id, Name, Qr Code, Url. These fields are necessary for the export to function correctly.', 'custom-qr-code-generator' ); ?>
			</p>
			<div id="error-message" style="color: red; display: none; font-size: 12px; padding-bottom: 10px;">
			</div>
			<label>
				<input type="checkbox" id="select-all" onclick="toggleCheckboxes( this )" checked> Select All
			</label><br>
			<?php

			foreach ( $columns as $column ) {
				$checked = in_array($column, $required_columns) ? 'checked disabled' : 'checked';
				echo '<label><input type="checkbox" name="columns[]" value="' . esc_attr( $column ) . '" ' . esc_attr( $checked ) . ' onchange="updateSelectAll()"> ' . esc_html( ucwords( str_replace( '_', ' ', $column ) ) ) . '</label><br>';
			}
			?>
			<br>
			<input type="submit" name="export" id="export-qrcode-report" class="button button-primary" value="<?php esc_attr_e( 'Export', 'custom-qr-code-generator' ); ?>">
		</form>
		<?php
	}

	/**
	 * Import the QR code table record
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function cqrc_import_qr_codes() {
		global $wpdb, $wp_filesystem;
		$table_name = esc_sql( QRCODE_GENERATOR_TABLE );

    	// Initialize WP Filesystem
		if ( ! WP_Filesystem() ) {
			echo '<h2 style="color:red;">' . esc_html__('Error: Could not initialize the filesystem.', 'custom-qr-code-generator') . '</h2>';
			return;
		}
		$available_options_tab = ! empty( $_GET['tab'] ) ? esc_html( sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) : 'cqrc_template_options';
		$sample_csv_file_url   = CQRCGEN_ADMIN_URL . '/assets/sample-file.csv';
		?>
		<h2><?php esc_html_e('Import QR Codes', 'custom-qr-code-generator'); ?></h2>
		<div class="cqrc-import-container">
			<div class="cqrc-instructions">
				<p><?php esc_html_e('To make the import process easier, please download the sample CSV file below to understand the required format:', 'custom-qr-code-generator'); ?></p>
			</div>
			<div class="cqrc-sample-download">
				<a href="<?php echo esc_url( $sample_csv_file_url ); ?>" class="button button-secondary" download>
					<?php esc_html_e('Download Sample CSV', 'custom-qr-code-generator'); ?>
				</a>
			</div>
			<hr class="site-hr-tag">
			<div class='cqrc-available-options-import'>
				<h3><?php esc_html_e( 'Available Default Options', 'custom-qr-code-generator' ); ?></h3>
				<small><?php esc_html_e( 'You can copy such default options from here and paste it in your csv file directly.', 'custom-qr-code-generator' ); ?></small>
				<nav class="nav-tab-wrapper">
					<a href="?page=custom-qrcode-import&tab=cqrc_template_options" class="nav-tab <?php echo ( 'cqrc_template_options' == $available_options_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Templates', 'custom-qr-code-generator' ) ; ?></a>
					<a href="?page=custom-qrcode-import&tab=cqrc_logo_options" class="nav-tab <?php echo ( 'cqrc_logo_options' == $available_options_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Logo', 'custom-qr-code-generator' ) ; ?></a>
					<a href="?page=custom-qrcode-import&tab=cqrc_frame_options" class="nav-tab <?php echo ( 'cqrc_frame_options' == $available_options_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Frame', 'custom-qr-code-generator' ) ; ?></a>
					<a href="?page=custom-qrcode-import&tab=cqrc_eye_frame_options" class="nav-tab <?php echo ( 'cqrc_eye_frame_options' == $available_options_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Eye Frame', 'custom-qr-code-generator' ) ; ?></a>
					<a href="?page=custom-qrcode-import&tab=cqrc_eye_balls_options" class="nav-tab <?php echo ( 'cqrc_eye_balls_options' == $available_options_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Eye Balls', 'custom-qr-code-generator' ) ; ?></a>
					<a href="?page=custom-qrcode-import&tab=cqrc_level_options" class="nav-tab <?php echo ( 'cqrc_level_options' == $available_options_tab ) ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Level', 'custom-qr-code-generator' ) ; ?></a>
				</nav>
				<div class="tab-content">
					<?php 
					switch($available_options_tab) {
						case 'cqrc_template_options':
						?>
						<div id='cqrc_template_options'>
							<h3><?php esc_html_e( 'Templates', 'custom-qr-code-generator' ) ; ?></h3>
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>template_name</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<?php
							$template_field_options = cqrc_get_template_field_options();
							if( ! empty( $template_field_options ) ) {
								// Loop through the options array
								$tp = 0;
								foreach ( $template_field_options as $option_value => $label ) {
									echo wp_kses_post( $cqrc_copy_cb_list_tp = $this->cqrc_generate_shortcode_html( $tp, esc_attr( $option_value ), esc_html( $label ) ) );
									$tp++;
								}
							}
							?>
						</div>
						<?php
						break;
						case 'cqrc_logo_options':
						?>
						<div id='cqrc_logo_options'>
							<h3><?php esc_html_e( 'Logo', 'custom-qr-code-generator' ) ; ?></h3>
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>upload_logo</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<?php 
							$upload_type_options = array( 
								'default' => esc_html__( 'Default', 'custom-qr-code-generator' ),
								'upload'  => esc_html__( 'Upload', 'custom-qr-code-generator' )
							);
							$ulg = 0;
							foreach ( $upload_type_options as $option_value => $label ) {
								echo wp_kses_post( $cqrc_copy_cb_list_ulg = $this->cqrc_generate_shortcode_html( $ulg, esc_attr( $option_value ), esc_html( $label ) ) );
								$ulg++;
							}
							?>
							<p style="color: #f00; font-weight:500;"><?php esc_html_e( 'For the "Upload" option above, only the following image formats are accepted: JPG, PNG, and WebP. Please note that animated WebP files are not supported. Ensure that you use either a non-animated WebP image or one of the other supported formats.', 'custom-qr-code-generator' ); ?></p>
							<hr class="site-hr-tag">
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>default_logo_name</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<h5><?php esc_html_e( 'The', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>upload_logo</code></pre><?php esc_html_e( ' field must be set as ', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>default</code></pre><?php esc_html_e( ' to use the options below.', 'custom-qr-code-generator' ); ?></h5>
							<div class="main-option">
								<?php
								$logo_field_options = cqrc_get_logo_field_options();
								if( ! empty( $logo_field_options ) ) {
									// Loop through the options array
									$lg = 0;
									foreach ( $logo_field_options as $option_value => $label ) {
										echo wp_kses_post( $cqrc_copy_cb_list_lg = $this->cqrc_generate_shortcode_html( $lg, esc_attr( $option_value ), esc_html( $label ) ) );
										$lg++;
									}
								}
								?>
							</div>
						</div>
						<?php
						break;
						case 'cqrc_frame_options':
						?>
						<div id='cqrc_frame_options'>
							<h3><?php esc_html_e( 'Frame', 'custom-qr-code-generator' ) ; ?></h3>
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>frame_name</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<?php
							$frame_field_options = cqrc_get_frame_field_options();
							if( ! empty( $frame_field_options ) ) {
								// Loop through the options array
								$fr = 0;
								foreach ( $frame_field_options as $option_value => $label ) {
									echo wp_kses_post( $cqrc_copy_cb_list_fr = $this->cqrc_generate_shortcode_html( $fr, esc_attr( $option_value ), esc_html( $label ) ) );
									$fr++;
								}
							}
							?>
						</div>
						<?php
						break;
						case 'cqrc_eye_frame_options':
						?>
						<div id='cqrc_eye_frame_options'>
							<h3><?php esc_html_e( 'Eye Frame', 'custom-qr-code-generator' ) ; ?></h3>
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>eye_frame_name</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<?php
							$eye_frame_field_options = cqrc_get_eye_frame_field_options();
							if( ! empty( $eye_frame_field_options ) ) {
								// Loop through the options array
								$ef = 0;
								foreach ( $eye_frame_field_options as $option_value => $label ) {
									echo wp_kses_post( $cqrc_copy_cb_list_ef = $this->cqrc_generate_shortcode_html( $ef, esc_attr( $option_value ), esc_html( $label ) ) );
									$ef++;
								}
							}
							?>
						</div>
						<?php
						break;
						case 'cqrc_eye_balls_options':
						?>
						<div id='cqrc_eye_balls_options'>
							<h3><?php esc_html_e( 'Eye Balls', 'custom-qr-code-generator' ) ; ?></h3>
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>eye_balls_name</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<?php
							$eye_balls_field_options = cqrc_get_eye_balls_field_options();
							if( ! empty( $eye_balls_field_options ) ) {
								// Loop through the options array
								$eb = 0;
								foreach ( $eye_balls_field_options as $option_value => $label ) {
									echo wp_kses_post( $cqrc_copy_cb_list_eb = $this->cqrc_generate_shortcode_html( $eb, esc_attr( $option_value ), esc_html( $label ) ) );
									$eb++;
								}
							}
							?>
						</div>
						<?php
						break;
						case 'cqrc_level_options':
						?>
						<div id='cqrc_level_options'>
							<h3><?php esc_html_e( 'Level', 'custom-qr-code-generator' ) ; ?></h3>
							<h4><?php esc_html_e( 'Field Id:', 'custom-qr-code-generator' ); ?><pre id="shortcode-code"><code>qrcode_level</code></pre></h4>
							<p><?php esc_html_e( 'Locate the field id inside the sample csv file and paste any value you copy from the below available options.', 'custom-qr-code-generator' ); ?></p>
							<?php
							$level_field_options = cqrc_get_level_field_options();
							if( ! empty( $level_field_options ) ) {
								// Loop through the options array
								$lv = 0;
								foreach ( $level_field_options as $option_value => $label ) {
									echo wp_kses_post( $cqrc_copy_cb_list_lv = $this->cqrc_generate_shortcode_html( $lv, esc_attr( $option_value ), esc_html( $label ) ) );
									$lv++;
								}
							}
							?>
						</div>
						<?php
						break;
					}
					?>
				</div>
			</div>
			<hr class="site-hr-tag">
			<form method="post" action="#"  id="cqrc-import-process-form" enctype="multipart/form-data" class="cqrc-upload-form">
				<?php wp_nonce_field('csv_import_action', 'csv_import_nonce_field'); ?>
				<p><strong><?php esc_html_e('Select your CSV file for import (Allowed Extentions: csv):', 'custom-qr-code-generator'); ?></strong></p>
				<input type="file" name="csv_file" accept=".csv" required class="input-file">
				<div class="cqrc-submit-btn">
					<input type="submit" name="submit"  class="button button-primary" value="<?php esc_attr_e('Import QR Codes', 'custom-qr-code-generator'); ?>">
				</div>
			</form>
			<div id="qrcode-loader" style="display: none;"></div>
		</div>
		<?php
		if (!empty($_POST['submit'])) {

			if ( empty($_REQUEST['csv_import_nonce_field']) || ! wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['csv_import_nonce_field'])), 'csv_import_action') ) {
				wp_die( esc_html__('Security check failed.', 'custom-qr-code-generator' ) );
			}
			if (!empty($_FILES['csv_file']['tmp_name'])) {
				// Sanitize the tmp_name
				$file_path = filter_var($_FILES['csv_file']['tmp_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

				// Check if the file is a CSV
				if (!empty($_FILES['csv_file']['name'])) {
					$file_type = wp_check_filetype(basename(sanitize_text_field($_FILES['csv_file']['name'])), null);
				}

				if ($file_type['ext'] !== 'csv') {
					echo '<h2 style="color:red;">' . esc_html__('Error: The uploaded file is not a valid CSV.', 'custom-qr-code-generator') . '</h2>';
					return;
				}

				$file_data = $wp_filesystem->get_contents($file_path);
				$lines = explode("\n", $file_data);
				$header = str_getcsv(array_shift($lines));
				$required_columns = ['id', 'name', 'url'];

				// Check for missing required columns
				foreach ( $required_columns as $column ) {
					if ( ! in_array( $column, $header ) ) {
						echo '<h2 style="color:red;">' . esc_html__('Error: Missing required column: ', 'custom-qr-code-generator') . esc_html($column) . '</h2>';
						return;
					}
				}

				// Initialize counters for new, updated, and skipped records
				$new_records          = 0;
				$updated_records      = 0;
				$skipped_records      = 0;
				$skipped_ids          = [];
				$skipped_line_numbers = [];
				$error_messages       = []; 

				foreach ( $lines as $line_number => $line ) {
					if ( empty( trim( $line ) ) ) continue;
					$row  = str_getcsv( $line );
					$id   = intval( $row[0] );
					$data = array(
						'url'                => ! empty( $row[1] ) ? esc_url( sanitize_url( $row[1] ) ) : '',
						'name'               => ! empty( $row[2] ) ? sanitize_text_field( $row[2] ) : '',
						'description'        => ! empty( $row[3] ) ? maybe_serialize( sanitize_textarea_field( $row[3] ) ) : '',
						'template_name'      => ! empty( $row[4] ) ? sanitize_text_field( $row[4] ) : '',
						'download'           => ! empty( $row[5] ) ? sanitize_text_field( $row[5] ) : '',
						'download_content'   => ! empty( $row[6] ) ? sanitize_text_field( $row[6] ) : '',
						'logo_option'        => ! empty( $row[7] ) ? sanitize_text_field( $row[7] ) : '',
						'default_logo'  	 => ! empty( $row[8] ) ? sanitize_text_field( $row[8] ) : '',
						'qr_code_color'      => ! empty( $row[9] ) ? sanitize_text_field( $row[9] ) : '',
						'default_frame'      => ! empty( $row[10] ) ? sanitize_text_field( $row[10] ) : '',
						'eye_frame_name'     => ! empty( $row[11] ) ? sanitize_text_field( $row[11] ) : '',
						'qr_eye_frame_color' => ! empty( $row[12] ) ? sanitize_text_field( $row[12] ) : '',
						'eye_balls_name'     => ! empty( $row[13] ) ? sanitize_text_field( $row[13] ) : '',
						'qr_eye_color'       => ! empty( $row[14] ) ? sanitize_text_field( $row[14] ) : '',
						'qrcode_level'       => ! empty( $row[15] ) ? sanitize_text_field( $row[15] ) : '',
						'password'           => ! empty( $row[16] ) ? sanitize_text_field( $row[16] ) : '',
						'updated_at'         => current_time( 'mysql' ),
					);

					// Initialize an array to store errors for this line
					$line_errors = [];

					$name = trim($data['name']);
					if (empty($name)) {
						$line_errors[] = esc_html__('Please enter a name.', 'custom-qr-code-generator');
					} elseif (!preg_match("/^[A-Za-z\s]+$/", $name)) {
						$line_errors[] = esc_html__('Name should contain only alphabetic characters and spaces.', 'custom-qr-code-generator');
					} elseif (strlen($name) > 30) {
						$line_errors[] = esc_html__('Name should not exceed 30 characters.', 'custom-qr-code-generator');
					}

				    // Trim and validate the URL
					$url = trim($data['url']);
					if (empty($url)) {
						$line_errors[] = esc_html__('Please enter a URL.', 'custom-qr-code-generator');
					} elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
						$line_errors[] = esc_html__('Please enter a valid URL.', 'custom-qr-code-generator');
					} elseif (strlen($url) < 16 || strlen($url) > 80) {
						$line_errors[] = esc_html__('URL must be between 16 and 80 characters.', 'custom-qr-code-generator');
					} else {
						$data['url'] = esc_url_raw($url);
					}

					$password = $data['password'];
					if (!empty($password)) {
						if (!preg_match('/[A-Z]/', $password)) {
							$line_errors[] = esc_html__('Password must contain at least one uppercase letter.', 'custom-qr-code-generator');
						}
						if (!preg_match('/[a-z]/', $password)) {
							$line_errors[] = esc_html__('Password must contain at least one lowercase letter.', 'custom-qr-code-generator');
						}
						if (!preg_match('/[0-9]/', $password)) {
							$line_errors[] = esc_html__('Password must contain at least one digit.', 'custom-qr-code-generator');
						}
						if (!preg_match('/[\W_]/', $password)) {
							$line_errors[] = esc_html__('Password must contain at least one special character.', 'custom-qr-code-generator');
						}
					}

					// Assuming this is within the foreach loop where you process each line
					$download_content = $data['download_content'];
					$download = $data['download'];
					$staticData = array("png", "jpg", "pdf");

					if (!empty($download_content)) {
						$download_items = array_map('trim', explode(',', $download_content));
						// $line_errors = [];

						foreach ($download_items as $index => $item) {
						    // Check length
							if ( strlen( $item ) > 15 ) {
								$line_errors[] = sprintf(
									/* translators: %s: The actual item value */
									esc_html__( '\'%s\' - Text must be between 0 and 15 characters long.', 'custom-qr-code-generator' ), 
									esc_html( $item )
								);
							}

						    // Check for alphabetic characters and spaces
							if ( ! preg_match( "/^[A-Za-z\s]+$/", $item ) ) {
								$line_errors[] = sprintf(
									/* translators: %s: The actual item value */
									esc_html__( '\'%s\' - Please enter only alphabetic characters and spaces between words.', 'custom-qr-code-generator' ), 
									esc_html( $item )
								);
							}
						}
					}

					if ( ! empty( $download ) ) {
						// $line_errors = [];
						$downloaditems = array_map( 'trim', explode( ',', $download ) );
						foreach ( $downloaditems as $index => $item ) {
					        // Check if the format is valid
							if ( ! in_array( strtolower( trim( $item ) ), $staticData ) ) {
								$line_errors[] = sprintf(
									/* translators: %s: The actual item value */
									esc_html__( '\'%s\' - Invalid format at the download field!', 'custom-qr-code-generator' ), 
									esc_html( $item )
								);
							}
						}
					}


                    // Check if default_logo is set to 'upload' and if default_logo_name is a valid URL
					if ($data['logo_option'] === 'upload' && !empty($data['default_logo'])) {
						$image_url = $data['default_logo'];
                        // Validate the URL
						if (!filter_var($image_url, FILTER_VALIDATE_URL)) {
							$error_messages[] = esc_html__('Invalid URL provided for the logo.', 'custom-qr-code-generator');
							$skipped_records++;
							$skipped_ids[] = 'Line ' . ($line_number + 2);
							continue;
						}

                        // Extract the filename from the URL
						$filename = basename(wp_parse_url($image_url, PHP_URL_PATH));
						$temp_name = pathinfo($image_url);
						$file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                        // Check if the file extension is one of the acceptable types
						$acceptable_extensions = ['jpeg', 'jpg', 'png', 'webp'];
						if (!in_array($file_extension, $acceptable_extensions)) {
							$error_messages[] = sprintf(
								/* translators: %s: The actual filename */
								esc_html__('\'%s\' - Invalid image type! Only JPEG, JPG, PNG, WebP are acceptable.', 'custom-qr-code-generator'), 
								esc_html($filename)
							);
							$skipped_records++;
							$skipped_ids[] = 'Line ' . ($line_number + 2);
							continue;
						}

						// Check if the image URL is accessible
						$headers = @get_headers($image_url);
						if (!$headers || strpos($headers[0], '200') === false) {
							$error_messages[] = sprintf(
								/* translators: %s: The actual filename */
								esc_html__('Image not found or inaccessible: %s', 'custom-qr-code-generator'),
								esc_html($image_url)
							);
							$skipped_records++;
							$skipped_ids[] = 'Line ' . ($line_number + 2);
							continue;
						}

    					// Additional error handling for WebP images
						if ($file_extension === 'webp') {
							$logo_image_resource = @imagecreatefromwebp($image_url);
							if ($logo_image_resource === false) {
								$error_messages[] = sprintf(
									/* translators: %s: The actual filename */
									esc_html__('Error: Animated WebP files cannot be read. Please use a non-animated WebP image or another supported format for %s.', 'custom-qr-code-generator'),
									esc_html($filename)
								);
								$skipped_records++;
								$skipped_ids[] = 'Line ' . ($line_number + 2);
								continue;
							}
						}

						// Verify that the domain or host of the image URL matches that of the existing website.
						$current_site_url = home_url();
						$siteUrl = wp_parse_url($current_site_url, PHP_URL_HOST);
						$inputurl = wp_parse_url($image_url, PHP_URL_HOST);

						if ($siteUrl === $inputurl) {
							$data['default_logo'] = $image_url;
						}else{
							$upload_dir = wp_upload_dir();

                            // Create a directory structure based on year and month
							$year_month_dir = $upload_dir['path'];

                            // Create the directory if it doesn't exist
							if (!is_dir($year_month_dir)) {
								wp_mkdir_p($year_month_dir);
							}

                            // Download the image data
							$image_data = @file_get_contents($image_url); // phpcs:ignore
							if ($image_data === false) {
								$error_messages[] = sprintf(
									/* translators: %s: The actual filename */
									esc_html__('Failed to download image from %s.', 'custom-qr-code-generator'),
									esc_html($image_url)
								);
								$skipped_records++;
								$skipped_ids[] = 'Line ' . ($line_number + 2);
								continue;
							}

                            // Parse the URL to get the query parameters
							$url_parts = wp_parse_url($image_url);
							$query_params = [];
							if ( !empty( $url_parts['query'] ) ) {
								parse_str($url_parts['query'], $query_params);
							}

                            // Create a new filename using the first parameter key-value pair
							$new_filename = $temp_name['filename'];
							foreach ($query_params as $key => $value) {
								$new_filename .= '-' . sanitize_file_name($value);
							}
							$new_filename .= '.' . $file_extension;

                            // Set the file path with the new filename
							$file = $year_month_dir . '/' . $new_filename;

                            // Save the image data to the file
							if (!file_exists($file)) {
								$result = @file_put_contents($file, $image_data); // phpcs:ignore
								if ($result === false) {
									$error_messages[] = sprintf(
										/* translators: %s: The actual filename */
										esc_html__('Failed to save image to %s.', 'custom-qr-code-generator'),
										esc_html($file)
									);
									$skipped_records++;
									$skipped_ids[] = 'Line ' . ($line_number + 2);
									continue;
								}

                                // Check the file type
								$wp_filetype = wp_check_filetype($new_filename, null);

                                // Prepare the attachment array
								$attachment = array(
									'post_mime_type' => $wp_filetype['type'],
									'post_title' => sanitize_file_name($new_filename),
									'post_content' => '',
									'post_status' => 'inherit'
								);

                                // Insert the attachment
								$attach_id = wp_insert_attachment($attachment, $file);

                                // Generate attachment metadata and update it
								if (!is_wp_error($attach_id)) {
									$attach_data = wp_generate_attachment_metadata($attach_id, $file);
									wp_update_attachment_metadata($attach_id, $attach_data);
								} else {
									$error_messages[] = sprintf(
										/* translators: %s: The actual filename */
										esc_html__('Failed to insert attachment for %s.', 'custom-qr-code-generator'),
										esc_html($new_filename)
									);
									$skipped_records++;
									$skipped_ids[] = 'Line ' . ($line_number + 2);
									continue;
								}
							}

                            // Update the 'default_logo' value with the new URL
							$data['default_logo'] = $upload_dir['url'] . '/' . $new_filename;
						}
					}

					// If this line has errors, accumulate them in $error_messages
					if (!empty($line_errors)) {
						$error_messages[] = sprintf('Line %d: %s', $line_number + 2, implode(' ', $line_errors));
						$skipped_records++;
						$skipped_ids[] = 'Line ' . ($line_number + 2);
						continue;
					}

					$existing_entry = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE id = %d", $id)); // phpcs:ignore

					if ( $existing_entry ) {
						$data['id'] = $row[0];
						$this->cqrc_generator_handle_data( $data, true, true );
						$updated_records++;
					}
					else {
						$data['id'] = '';
						$img_src    = ! empty( $data['qr_code'] ) ? $data['qr_code'] : '';
						if ( ! empty( $img_src ) ) {
							$lastid_val = $wpdb->get_var( $wpdb->prepare( "SELECT `id` FROM {$table_name} ORDER BY id DESC LIMIT 1" ) ); // phpcs:ignore
							$lastid = (!empty($lastid_val) && $lastid_val !== '0' ? $lastid_val+1 : 1);
							$new_file_name =  "wwt-qrcode-{$lastid}";
							$upload_dir = wp_upload_dir();
							$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $img_src);

							if (!file_exists($file_path)) {
								$skipped_records++;
								$skipped_ids[] = 'Line ' . ( $line_number + 2 ). ' "Qr Code not exist!"';
								continue;
							}

							$file_info = pathinfo( $file_path, PATHINFO_EXTENSION );
							$new_file_paths = $upload_dir['path'] . '/' . $new_file_name . '.' . $file_info;

							if (!$wp_filesystem->copy($file_path, $new_file_path)) {
								$skipped_records++;
								$skipped_ids[] = 'Line ' . ( $line_number + 2 ). ' "Qr Code not exist!"';
								continue;
							}

							$attachment = array(
								'guid'           => $upload_dir['baseurl'] . '/' . $new_file_name . '.' . $file_info,
								'post_mime_type' => wp_check_filetype(basename( $new_file_path ), null)['type'],
								'post_title'     => sanitize_file_name( $new_file_name ),
								'post_content'   => '',
								'post_status'    => 'inherit'
							);

							$attach_id   = wp_insert_attachment( $attachment, $new_file_path );
							$attach_data = wp_generate_attachment_metadata( $attach_id, $new_file_path );
							wp_update_attachment_metadata( $attach_id, $attach_data );
							$qrcode_new  = wp_get_attachment_url( $attach_id );

							if ( ! empty( $qrcode_new ) && ! empty( $qrcode_new ) ) {
								$data['qr_code'] = $qrcode_new;
							}
						}
						else {
							if ( ! empty($data['description'] ) ) {
								$data['description'] = maybe_unserialize( $data['description'] );
							}

							$this->cqrc_generator_handle_data( $data, true, true );
							$new_records++; 
						}
					}
				}

	            // Display the result message in a user-friendly way using WordPress styles
				echo '<div class="notice notice-success is-dismissible" style="margin: 30px 0;border-radius: 5px;">';
				echo '<p><strong>' . esc_html__( 'Import Successful!', 'custom-qr-code-generator' ) . '</strong></p>';
				echo '<p>' . esc_html__( 'New Records Added: ', 'custom-qr-code-generator' ) . esc_attr( $new_records ) . '</p>';
				echo '<p>' . esc_html__( 'Records Updated: ', 'custom-qr-code-generator' ) . esc_attr( $updated_records ) . '</p>';
				echo '<p>' . esc_html__( 'Records Skipped: ', 'custom-qr-code-generator' ) . esc_attr( $skipped_records ) . '</p>';
				echo '</div>';

        		// Display errors in a user-friendly way using a list
				if (!empty($error_messages)) {
					echo '<div class="notice notice-error is-dismissible site-show-error-msg" style="margin: 30px 0; border-radius: 5px; background-color: #f8d7da; color: #721c24; padding: 20px;">';
					echo '<p><strong>' . esc_html__( 'Failed Records (Skipped due to validation errors)', 'custom-qr-code-generator' ) . ' - (' . esc_attr( $skipped_records ) . ')</strong></p>';

    				// Only show error messages if there are skipped records
					if ($skipped_records > 0) {
						echo '<ul style="list-style-type: disc; padding-left: 20px;">';
						foreach ($error_messages as $message) {
							echo '<li>' . esc_html($message) . '</li>';
						}
						echo '</ul>';
					}

					echo '</div>';
				}
			}
		}
	}

	/**
	 * CB function to generate the copy message from the shortcode called.
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	private function cqrc_generate_shortcode_html( $tp, $option_value, $label ) {
		$html = '<div class="shortcode-list-cqrc">';
		$html .= '<span class="shortcode" id="copy-code-icon-' . esc_attr($tp) . '" data-clipboard-text="' . esc_attr($option_value) . '">';
		$html .= '<pre id="shortcode-code"><code>' . esc_html($label) . '</code>';
		$html .= '<span class="message copy-code-icons-list-wrap" style="display: none; color: green; margin-left: 10px;">' . esc_html__('Code copied!!!', 'custom-qr-code-generator') . '</span>';
		$html .= '</pre>';
		$html .= '<span id="copy-code-icons" class="dashicons dashicons-admin-page" style="cursor: pointer; font-size: 20px; margin-left: 10px; margin-right: 20px;" title="Copy to clipboard"></span>';
		$html .= '</span>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * CB function to check if the WebP image is animated.
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function cqrc_check_animated_webp() {
		if (empty($_REQUEST['_ajax_nonce']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_ajax_nonce'])), 'qr_code_nonce')) {
			wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator'));
		}

		if ( !empty( $_POST['image_url'] ) ) {
			$image_url = !empty($_POST['image_url']) ? sanitize_url(wp_unslash($_POST['image_url'])) : '';
			$file_extension = pathinfo($image_url, PATHINFO_EXTENSION);
			
			if ($file_extension === 'webp') {
				$logo_image_resource = @imagecreatefromwebp($image_url);
				if ($logo_image_resource === false) {
					wp_send_json_success([ 'message' => 'is_animated']);
				} else {
					wp_send_json_success([ 'message' => 'non-animated']);
				}
			}
		}
		wp_send_json_error();
	}

	/**
	 * Listing Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function cqrc_form_page() {
		require_once CQRCGEN_ADMIN_DIR . '/partials/cqrc-generate-form.php';
	}

	/**
	 * QRCode Helper function to handle the qrcode previour data.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_handle_qrurl_insert_record() {
		// Verify the nonce before processing further
		if (empty($_REQUEST['_ajax_nonce']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_ajax_nonce'])), 'qr_code_nonce')) {
			wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator'));
		}

		global $wpdb;
		$site_url   = site_url();
		$table_name = esc_sql( QRCODE_GENERATOR_TABLE );

		if (empty($_POST['qrcode_url'])) {
			wp_send_json_error(['message' => 'Invalid URL.']);
			return;
		}

		$data = [
			'urls' => !empty($_POST['qrcode_url']) ? sanitize_url(wp_unslash($_POST['qrcode_url'])) : '',
			'qrid' => !empty($_POST['qrid']) ? sanitize_text_field(wp_unslash($_POST['qrid'])) : '',
			'name' => !empty($_POST['qrcode_name']) ? sanitize_text_field(wp_unslash($_POST['qrcode_name'])) : '',
			'template_name' => !empty($_POST['template_name']) ? sanitize_text_field(wp_unslash($_POST['template_name'])) : '',
			'logo_option' => !empty($_POST['logo_option']) ? sanitize_text_field(wp_unslash($_POST['logo_option'])) : '',
			'upload_logo_url' => !empty($_POST['upload_logo_url']) ? sanitize_url(wp_unslash($_POST['upload_logo_url'])) : '',
			'default_logo' => !empty($_POST['default_logo']) ? sanitize_text_field(wp_unslash($_POST['default_logo'])) : '',
			'default_frame' => !empty($_POST['default_frame']) ? sanitize_text_field(wp_unslash($_POST['default_frame'])) : '',
			'eye_frame_name' => !empty($_POST['eye_frame_name']) ? sanitize_text_field(wp_unslash($_POST['eye_frame_name'])) : '',
			'eye_balls_name' => !empty($_POST['eye_balls_name']) ? sanitize_text_field(wp_unslash($_POST['eye_balls_name'])) : '',
			'qrcode_level' => !empty($_POST['qrcode_level']) ? sanitize_text_field(wp_unslash($_POST['qrcode_level'])) : 'QR_ECLEVEL_Q',
			'qr_eye_frame_color' => !empty($_POST['qr_eye_frame_color']) ? sanitize_hex_color(wp_unslash( $_POST['qr_eye_frame_color'])) : '',
			'qr_eye_color' => !empty($_POST['qr_eye_color']) ? sanitize_hex_color(wp_unslash( $_POST['qr_eye_color'])) : '',
			'qr_code_color' => !empty($_POST['qr_code_color']) ? sanitize_hex_color(wp_unslash( $_POST['qr_code_color'])) : ''
		];

		$download 		   = !empty($_POST['download']) ? array_map('sanitize_text_field', $_POST['download']) : []; // phpcs:ignore
		$download_string   = !empty($download) ? implode(',', $download) : ''; 
		$download_text_png = !empty($_POST['download_text_png']) ? sanitize_text_field(wp_unslash( $_POST['download_text_png'])) : '';
		$download_text_jpg = !empty($_POST['download_text_jpg']) ? sanitize_text_field(wp_unslash( $_POST['download_text_jpg'])) : '';
		$download_text_pdf = !empty($_POST['download_text_pdf']) ? sanitize_text_field(wp_unslash( $_POST['download_text_pdf'])) : '';
		$show_desc_in_pdf = !empty($_POST['show_desc_in_pdf']) ? sanitize_text_field(wp_unslash( $_POST['show_desc_in_pdf'])) : '';
		$download_texts = [
			'png' => $download_text_png,
			'jpg' => $download_text_jpg,
			'pdf' => $download_text_pdf,
			'show_desc_in_pdf' => $show_desc_in_pdf
		];

		$download_texts_json = wp_json_encode($download_texts);
		$data['download'] = $download_string;
		$data['download_content'] = $download_texts;

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
			$result = $this->cqrc_qr_code_generate_process($table_name, $data, $paths, $site_url);
		} else {
			$result = $this->cqrc_qr_code_generate_process($table_name, $data, $paths, $site_url, $data['qrid']);
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
				'default_logo' => esc_html__('facebook', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('frame14', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('ball18', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#2c4270', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#2c4270', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#2c4270', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_H', 'custom-qr-code-generator')
			],
			'youtube-circle' => [
				'default_logo' => esc_html__('youtube-circle', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('frame13', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('ball14', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#BF2626', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#EE0F0F', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#EE0F0F', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_Q', 'custom-qr-code-generator')
			],
			'twitter-circle' => [
				'default_logo' => esc_html__('twitter-circle', 'custom-qr-code-generator' ),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator' ),
				'eye_frame_name' => esc_html__('frame5', 'custom-qr-code-generator' ),
				'eye_balls_name' => esc_html__('ball11', 'custom-qr-code-generator' ),
				'qr_code_color' => esc_html__('#55ACEE', 'custom-qr-code-generator' ),
				'qr_eye_color' => esc_html__('#55ACEE', 'custom-qr-code-generator' ),
				'qr_eye_frame_color' => esc_html__('#55ACEE', 'custom-qr-code-generator' ),
				'qrcode_level' => esc_html__('QR_ECLEVEL_Q', 'custom-qr-code-generator' )
			],
			'instagram-circle' => [
				'default_logo' => esc_html__('instagram-circle', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('frame5', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('ball4', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#0d1766', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#0d1766', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#8224e3', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_H', 'custom-qr-code-generator')
			],
			'whatsapp-circle' => [
				'default_logo' => esc_html__('whatsapp-circle', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('frame2', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('ball2', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#2ebd38', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#2ebd38', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#2ebd38', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_M', 'custom-qr-code-generator')
			],
			'gmail' => [
				'default_logo' => esc_html__('gmail', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('frame14', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('ball14', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#e4594c', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#e4594c', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#e4594c', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_Q', 'custom-qr-code-generator')
			],
			'linkedin-circle' => [
				'default_logo' => esc_html__('linkedin-circle', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('frame0', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('ball0', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#005881', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#005881', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#005881', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_M', 'custom-qr-code-generator')
			],
			'default' => [
				'default_logo' => esc_html__('default', 'custom-qr-code-generator'),
				'default_frame' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_frame_name' => esc_html__('default', 'custom-qr-code-generator'),
				'eye_balls_name' => esc_html__('default', 'custom-qr-code-generator'),
				'qr_code_color' => esc_html__('#000000', 'custom-qr-code-generator'),
				'qr_eye_color' => esc_html__('#000000', 'custom-qr-code-generator'),
				'qr_eye_frame_color' => esc_html__('#000000', 'custom-qr-code-generator'),
				'qrcode_level' => esc_html__('QR_ECLEVEL_M', 'custom-qr-code-generator')
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
			return $data['upload_logo_url'];
		}
		return '';
	}

	/**
	 * QRCode Helper function to process QR code creation and database update.
	 *
	 * @since    1.0.0
	 */
	private function cqrc_qr_code_generate_process($table_name, $data, $paths, $site_url, $qrid = null) {
		global $wpdb;
		
		// Prepare base URL
		$base_url = $site_url . '/qrcode_scan';

	    // Create nonce for security
		$qrcode_scan_nonce = wp_create_nonce('qrcode_scan_nonce');

	    // If qrid is provided, use it to determine the URL parameters
		if (!empty($qrid)) {
	        $prev_qrcode = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_name ORDER BY id DESC LIMIT %d", 1)); // phpcs:ignore
	        $prev_id = $prev_qrcode ? $prev_qrcode->id : 0;
	        $identifier_with_suffix = $prev_id . 'FTA';

	        // Build URL with query arguments
	        $url = add_query_arg(array(
	        	'url' => bin2hex($data['urls']),
	        	'qrid' => $identifier_with_suffix,
	        	'qrcode_wpnonce' => $qrcode_scan_nonce,
	        ), $base_url);
	    } else {
	    	$prev_id = 0;

	        // Build URL with query arguments for the case where qrid is not provided
	    	$url = add_query_arg(array(
	    		'url' => bin2hex($data['urls']),
	    		'previd' => 'PREV001',
	    		'qrcode_wpnonce' => $qrcode_scan_nonce,
	    	), $base_url);
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
	    			return ['success' => false, 'message' => esc_html__('Database update failed.', 'custom-qr-code-generator')];
	    		}
	    	}
			// phpcs:enable
        	// Return the successful response with QR code URL and QRID
	    	return [
	    		'success' => true,
	    		'message' => esc_html__('QR code processed successfully.', 'custom-qr-code-generator' ),
	    		'url_data' => $qr_code_url,
	    		'ext_id' => $qrid
	    	];
	    }

	    return ['success' => false, 'message' => esc_html__('QR code generation failed.', 'custom-qr-code-generator')];
	}

	/**
	 * QRCode generation form submission handle.
	 * @since    1.0.0
	 */
	public function cqrc_handle_qr_code_generate_action($url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $is_form_submitted = true) {
		return $this->cqrc_generate_qr_code_image($url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $is_form_submitted = true);
	}

	/**
	 * QRCode generation form submission handle.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_generator_form_handle() {
		global $wpdb;
		$table_name = esc_sql( QRCODE_GENERATOR_TABLE );

		if ( ! empty( $_POST['qrcode_url'] ) && ! empty( $_POST['qrcode_name'] ) ) {
			if ( empty( $_POST['qr_code_form_data_nonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qr_code_form_data_nonce'] ) ), 'qr_code_form_data' ) ) {
				return;
			}

	        // Prepare data from $_POST
			$data = array(
				'id'                  => ! empty( $_POST['qrid'] ) ? sanitize_text_field( wp_unslash( $_POST['qrid'] ) ) : '',
				'url'                 => ! empty( $_POST['qrcode_url'] ) ? sanitize_text_field( wp_unslash($_POST['qrcode_url'])) : '',
				'name'                => ! empty( $_POST['qrcode_name'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_name'] ) ) : '',
				'description'         => ! empty( $_POST['qrcode_description'] ) ? wp_kses_post( wp_unslash( $_POST['qrcode_description'] ) ) : '',
				'template_name'       => ! empty( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash($_POST['template_name'] )) : '',
				'download'            => ! empty( $_POST['download'] ) ? array_map( 'sanitize_text_field', $_POST['download'] ) : [], // phpcs:ignore
				'download_text_png'   => ! empty( $_POST['download_text_png'] ) ? sanitize_text_field(wp_unslash( $_POST['download_text_png'])) : '',
				'download_text_jpg'   => ! empty( $_POST['download_text_jpg'] ) ? sanitize_text_field(wp_unslash( $_POST['download_text_jpg'])) : '',
				'download_text_pdf'   => ! empty( $_POST['download_text_pdf'] ) ? sanitize_text_field(wp_unslash( $_POST['download_text_pdf'])) : '',
				'show_desc_in_pdf'    => ! empty( $_POST['show_desc_in_pdf'] ) ? sanitize_text_field(wp_unslash( $_POST['show_desc_in_pdf'])) : '',
				'logo_option'         => ! empty( $_POST['logo_option'] ) ? sanitize_text_field( wp_unslash( $_POST['logo_option'] ) ) : '',
				'default_logo'        => ! empty( $_POST['default_logo'] ) ? sanitize_text_field( wp_unslash( $_POST['default_logo'] ) ) : '',
				'upload_logo_url'     => ! empty( $_POST['upload_logo_url'] ) ? sanitize_text_field( wp_unslash( $_POST['upload_logo_url'] ) ) : '',
				'qr_code_color'       => ! empty( $_POST['qr_code_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_code_color'] )) : '',
				'default_frame'       => ! empty( $_POST['default_frame'] ) ? sanitize_text_field( wp_unslash( $_POST['default_frame'] ) ) : '',
				'eye_frame_name'      => ! empty( $_POST['eye_frame_name'] ) ? sanitize_text_field( wp_unslash( $_POST['eye_frame_name'] ) ) : '',
				'qr_eye_frame_color'  => ! empty( $_POST['qr_eye_frame_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_eye_frame_color'] )) : '',
				'eye_balls_name'      => ! empty( $_POST['eye_balls_name'] ) ? sanitize_text_field( wp_unslash( $_POST['eye_balls_name'] ) ) : '',
				'qr_eye_color'        => ! empty( $_POST['qr_eye_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_eye_color'] )) : '',
				'password'            => ! empty( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '',
				'qrcode_level'        => ! empty( $_POST['qrcode_level'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_level'] ) ) : 'QR_ECLEVEL_H',
			);

	        // Call the common function with the prepared data
			return $this->cqrc_generator_handle_data( $data );
		}
	}

	/**
	 * handle the QRCode data  function PHPQRCODE.
	 * @since 1.0.0
	 */
	private function cqrc_generator_handle_data( $data, $redirect_case = false, $is_image_url = false ) {
		global $wpdb;
		$table_name = esc_sql( QRCODE_GENERATOR_TABLE );

	    // Initialize variables
		$framepath = '';
		$eye_framepath = '';
		$eye_balls_path = '';
		$u_id = get_current_user_id();
		$uploaded_logo = '';
		$logopath = '';
		$description = maybe_unserialize( ! empty( $data['description'] ) ? $data['description'] : '' );

		$template_record = $this->cqrc_get_qrcode_settings($data['template_name']);
		$data['default_logo'] = !empty( $data['default_logo'] ) ? $data['default_logo'] : $template_record['default_logo'];
		$data['default_frame'] = !empty( $data['default_frame'] ) ? $data['default_frame'] : $template_record['default_frame'];
		$data['eye_frame_name'] = !empty( $data['eye_frame_name'] ) ? $data['eye_frame_name'] : $template_record['eye_frame_name'];
		$data['eye_balls_name'] = !empty( $data['eye_balls_name'] ) ? $data['eye_balls_name'] : $template_record['eye_balls_name'];
		$data['qr_code_color'] = !empty( $data['qr_code_color'] ) ? $data['qr_code_color'] : $template_record['qr_code_color'];
		$data['qr_eye_color'] = !empty( $data['qr_eye_color'] ) ? $data['qr_eye_color'] : $template_record['qr_eye_color'];
		$data['qr_eye_frame_color'] = !empty( $data['qr_eye_frame_color'] ) ? $data['qr_eye_frame_color'] : $template_record['qr_eye_frame_color'];
		$data['qrcode_level'] = !empty( $data['qrcode_level'] ) ? $data['qrcode_level'] : $template_record['qrcode_level'];
		
	    // Handle frame paths
		if ('default' == $data['default_frame']) {
			$framepath = '';
		} else if ('' !== $data['default_frame']) {
			$framepath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/' . $data['default_frame'] . '.png';
		}

		if ('default' == $data['eye_frame_name']) {
			$eye_framepath = '';
		} else if ('' !== $data['eye_frame_name']) {
			$eye_framepath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/' . $data['eye_frame_name'] . '.png';
		}

		if ('default' == $data['eye_balls_name']) {
			$eye_balls_path = '';
		} else if ('' !== $data['eye_balls_name']) {
			$eye_balls_path = CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/' . $data['eye_balls_name'] . '.png';
		}

	    // Handle logo paths
		if ('default' === $data['logo_option']) {
			if (basename($data['default_logo']) == 'default') {
				$logopath = $data['default_logo'];
			} elseif ('' !== $data['default_logo']) {
				$logopath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/logos/' . basename($data['default_logo']) . '.png';
			}
		} elseif ('upload' === $data['logo_option']) {
			if ($is_image_url === false && filter_var( $data['upload_logo_url'], FILTER_VALIDATE_URL ) ) {
				$logopath = $data['upload_logo_url'];
			}else{
				$logopath = $data['default_logo'];
			}
		}else{
			$logopath = CQRCGEN_ADMIN_DIR . '/assets/qrcode/logos/' . basename($data['default_logo']) . '.png';
		}

	    // Prepare download content
		$download_content = [];
		if (!empty($data['download']) && is_array($data['download'])) {
			$download_arr = $data['download'];
			$default_titles = array(
				'png' => esc_html__('Download PNG', 'custom-qr-code-generator'),
				'jpg' => esc_html__('Download JPG', 'custom-qr-code-generator'),
				'pdf' => esc_html__('Download PDF', 'custom-qr-code-generator'),
			);
			foreach ($download_arr as $key => $download_type) {
				$dow_type = strtolower($download_type);
				if (!array_key_exists($dow_type, $download_content)) {
					$download_content[$dow_type] = !empty($data['download_text_' . $dow_type]) ? $data['download_text_' . $dow_type] : ($default_titles[$dow_type] ?? '');
				}
			}
		}

		// Add 'show_desc_in_pdf' field at the end
		if (!empty($data['show_desc_in_pdf'])) {
			$download_content['show_desc_in_pdf'] = esc_html($data['show_desc_in_pdf']);
		}
		
	    // Set default values if empty
		$qr_code_color = !empty($data['qr_code_color']) ? $data['qr_code_color'] : '#000000';
		$qrcode_level = !empty($data['qrcode_level']) ? $data['qrcode_level'] : 'QR_ECLEVEL_M';

		// Ensure $data['download'] is a valid array before using implode()
		$download_string = (!empty($data['download']) && is_array($data['download'])) ? implode(',', $data['download']) : '';

		$current_time = gmdate('Y-m-d H:i:s', current_time('timestamp', true));

		$update_data = array(
			'id'            	 => $data['id'],
			'user_id'            => $u_id,
			'url'                => $data['url'],
			'name'               => $data['name'],
			'description'        => $description,
			'template_name'      => $data['template_name'],
			'download'           => $download_string,
			'download_content'   => !empty($download_content) ? wp_json_encode($download_content) : '',
			'upload_logo'        => 'upload' === $data['logo_option'] ? 'upload' : 'default',
			'default_logo_name'  => ( $is_image_url === false && filter_var( $data['upload_logo_url'], FILTER_VALIDATE_URL ) ) ? $data['upload_logo_url'] : ( ( 'upload' === $data['logo_option'] ) ? ( !empty( $data['default_logo'] ) ? $data['default_logo'] : '' ) : $data['default_logo'] ),
			'logo_type'          => 'PNG',
			'qr_eye_color'       => $data['qr_eye_color'],
			'frame_name'         => $data['default_frame'],
			'eye_frame_name'     => $data['eye_frame_name'],
			'qr_eye_frame_color' => $data['qr_eye_frame_color'],
			'eye_balls_name'     => $data['eye_balls_name'],
			'qr_code_color'      => $qr_code_color,
			'qrcode_level'       => $qrcode_level,
			'token'              => '',
			'password'           => $data['password'],
			'created_at'         => $current_time,
		);

		// Handle insert/update operation
		$this->cqrc_handle_insert_update_cb( $wpdb, $table_name, $update_data, $logopath, $framepath, $eye_framepath, $eye_balls_path, $qr_code_color, $qrcode_level, $redirect_case);
	}

	/**
	 * QRCode handle insert update callback function.
	 * @since 1.0.0
	 */
	private function cqrc_handle_insert_update_cb( $wpdb, $table_name, $data, $logopath, $framepath, $eye_framepath, $eye_balls_path, $qr_code_color, $qrcode_level, $redirect_case) {
		
		if ( !empty( $data['id'] ) ) {
			$id = $data['id'];

	        // Update existing record
	        // phpcs:disable
			$qr_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ), ARRAY_A );
			if ( $qr_data ) {
				$existing_qr_code_url = $qr_data['qr_code'];
				$posts_to_delete = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid = %s", $existing_qr_code_url ) );

				foreach ( $posts_to_delete as $post ) {
					wp_delete_post( $post->ID, true );
				}

				$wpdb->update( $table_name, $data, array( 'id' => $id ), 
					array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ), 
					array( '%d' ) 
				);
			}
	        // phpcs:enable
		} else {
	        // Insert new record
			$data['total_scans'] = '0';

	        // phpcs:disable
			$wpdb->insert( $table_name, $data, 
				array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) 
			);
	        // phpcs:enable

			if ( false === $wpdb->insert_id ) {
				echo 'Database insertion error: ' . esc_html( $wpdb->last_error );
				return false;
			}

			$id = $wpdb->insert_id;
		}

	    // Generate QR code
		$identifier_with_suffix = $id . 'FTA';
		$qrcode_scan_nonce = wp_create_nonce( 'qrcode_scan_nonce' );
		$url = add_query_arg( array(
			'url'             => bin2hex( $data['url'] ),
			'qrid'            => $identifier_with_suffix,
			'qrcode_wpnonce'  => $qrcode_scan_nonce
		), site_url( '/qrcode_scan' ) );

		$qr_code_url = $this->cqrc_generator_create_qr_code( $url, $id, $logopath, $framepath, $eye_framepath, $eye_balls_path, $data['qr_eye_color'], $data['qr_eye_frame_color'], $qr_code_color, $qrcode_level, $data['password'], $remove_existing = true);

		if ( is_wp_error( $qr_code_url ) ) {
			echo 'Error generating QR code: ' . esc_html( $qr_code_url );
			return false;
		}

	    // Update QR code in database
	    // phpcs:disable
		$wpdb->update( $table_name, array( 'qr_code' => $qr_code_url ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	    // phpcs:enable

		if ( $redirect_case ) {
			return true;
		} else {
			$redirect_url = add_query_arg( array( 'page' => 'custom-qr-code-generator' ), admin_url( 'admin.php' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * QRCode generate function PHPQRCODE.
	 * @since 1.0.0
	 */
	public function cqrc_generator_create_qr_code($url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password, $is_form_submitted = false, $remove_existing = true) {
		return $this->cqrc_generate_qr_code_image($url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password, $is_form_submitted = false, $remove_existing = true);
	}

	/**
	 * Helper method for generate the qrcode image process.
	 * @since 1.0.0
	 */
	private function cqrc_generate_qr_code_image($url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password = '', $is_form_submitted = true, $remove_existing = false) {
		global $wpdb;
		$merged_image_resource = '';
		$table_name = esc_sql( QRCODE_GENERATOR_TABLE );

	    // QR Code Black & White Combination Fixes.
		switch ( $qr_code_color ) {
			case '#ffffff':
			$qr_code_bg_color = 0;
			$qr_eye_color = '#ffffff';
			break;
			case '#000000':
			$qr_code_bg_color = 16777215;
			$qr_code_color = '#000000';
			break;
			default:
			$qr_code_bg_color = 16777215;
		}

	    // Ensure colors are valid
		$qr_eye_frame_color = $this->cqrc_hex_to_rgb( $qr_eye_frame_color );
		$qr_code_colors = [];
		if ( $qr_code_color !== '' ) {
			$qr_code_colors = $this->cqrc_hex_to_rgb( $qr_code_color );
			if ( $qr_code_colors === false ) {
				wp_die( esc_html__( 'Invalid QR code color format.', 'custom-qr-code-generator' ) );
			}
		}

		$qr_eye_rgb = $this->cqrc_hex_to_rgb( $qr_eye_color );
	    // QR Code Level Constants Conversion.
		switch ( $qrcode_level ) {
			case 'QR_ECLEVEL_Q':
			$qrcode_level = QRCode::ECC_Q;
			break;
			case 'QR_ECLEVEL_H':
			$qrcode_level = QRCode::ECC_H;
			break;
			case 'QR_ECLEVEL_M':
			$qrcode_level = QRCode::ECC_M;
			break;
			default:
			$qrcode_level = QRCode::ECC_H;
		}

	    // Handle password and token logic if needed
		if ( $password !== '' || !empty( $password ) ) {
			$token = bin2hex( random_bytes(16) );
	        // phpcs:disable
			$wpdb->update(
				$table_name,
				array( 'token' => $token ),
				array( 'id' => $id ),
				array( '%s' ),
				array( '%d' )
			);
	        // phpcs:enable
			$data = add_query_arg( 'token', $token, $url );
		} else {
			$data = $url;
	        // phpcs:disable
			$wpdb->update(
				$table_name,
				array( 'token' => '', 'password' => '' ),
				array( 'id' => $id ),
				array( '%s', '%s' ),
				array( '%d' )
			);
	        // phpcs:enable
		}

	    // QR Code generation
		$options = new QROptions([
			'outputType' => QRCode::OUTPUT_IMAGE_PNG,
			'eccLevel' => $qrcode_level,
			'scale' => 15,
		]);

		ob_start();
		$qrcode = new QRCode( $options );
		$qr_image = $qrcode->render( $data );
		ob_end_clean();

	    // Process the QR code image
		$base64_image = str_replace( 'data:image/png;base64,', '', $qr_image );
		$base64_image = str_replace( ' ', '+', $base64_image );
		$image_data = base64_decode( $base64_image );
		$upload_dir = wp_upload_dir();
		$file_path = $upload_dir['path'] .'/qrcode.png';

		global $wp_filesystem;

	    // Initialize the WP_Filesystem API.
		if ( false === ( $creds = request_filesystem_credentials( site_url() ) ) ) {
			return;
		}

	    // Check if we can initialize the filesystem.
		if ( !WP_Filesystem( $creds ) ) {
			wp_die( esc_html__( 'Could not initialize WP_Filesystem.', 'custom-qr-code-generator' ) );
		}

	    // Use the WP_Filesystem to write the file.
		if ( !$wp_filesystem->put_contents( $file_path, $image_data, FS_CHMOD_FILE ) ) {
			wp_die( esc_html__( 'Failed to save QR code image.', 'custom-qr-code-generator' ) );
		}

		$qr_image_resource = imagecreatefromstring( $image_data );
		if ( $qr_image_resource === false ) {
			wp_die( esc_html__( 'Failed to create image from string.', 'custom-qr-code-generator' ) );
		}

	    // Define the new foreground color
	    $fgColor = imagecolorallocate( $qr_image_resource, $qr_code_colors['r'], $qr_code_colors['g'], $qr_code_colors['b'] ); // RGB for the desired color
	    $whiteColor = imagecolorallocate( $qr_image_resource, 255, 255, 255 ); // RGB for White

	    // Iterate through each pixel to change the color from black to your desired color
	    $qr_width = imagesx( $qr_image_resource );
	    $qr_height = imagesy( $qr_image_resource );

	    if ( $qr_width === false || $qr_height === false ) {
	    	wp_die( esc_html__( 'Failed to get image dimensions.', 'custom-qr-code-generator' ) );
	    }

	    // Define the eye area positions (top-left, top-right, bottom-left)
	    $eyeAreas = [
	        ['x' => 60, 'y' => 60, 'size' => 7],  // Top-left eye
	        ['x' => $qr_width - 15 * 12, 'y' => 60, 'size' => 8],  // Top-right eye
	        ['x' => 60, 'y' => $qr_height - 12 * 15, 'size' => 8],  // Bottom-left eye
	    ];

	    // Iterate through each pixel to change the color
	    for ( $y = 0; $y < $qr_height; $y++ ) {
	    	for ( $x = 0; $x < $qr_width; $x++ ) {
	    		$currentColor = imagecolorat( $qr_image_resource, $x, $y );
	            // Skip the eye areas to avoid changing their color
	    		$inEyeArea = false;
	    		foreach ( $eyeAreas as $eyeArea ) {
	    			if ( $x >= $eyeArea['x'] && $x < $eyeArea['x'] + $eyeArea['size'] * 15 &&
	    				$y >= $eyeArea['y'] && $y < $eyeArea['y'] + $eyeArea['size'] * 15 ) {
		    				$inEyeArea = true;
		    			break;
		    		}
		    	}
		    	if ( $inEyeArea && $currentColor == 0 ) {
		    		imagesetpixel( $qr_image_resource, $x, $y, $whiteColor );
		    	} elseif ( !$inEyeArea && $currentColor == 0 ) {
		    		imagesetpixel( $qr_image_resource, $x, $y, $fgColor );
		    	}
		    }
		}

			    // Eye frame processing
		$eyeFrame = !empty( $eye_frame_image ) ? imagecreatefrompng( $eye_frame_image ) : imagecreatefrompng( CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-frames/frame0.png' );
		$eyeFrameWidth = imagesx( $eyeFrame );
		$eyeFrameHeight = imagesy( $eyeFrame );
		$scaleFactor = 2.1;
		$scaledEyeFrameWidth = $eyeFrameWidth * $scaleFactor;
		$scaledEyeFrameHeight = $eyeFrameHeight * $scaleFactor;
		$scaledEyeFrame = imagecreatetruecolor( $scaledEyeFrameWidth, $scaledEyeFrameHeight );
		imagealphablending( $scaledEyeFrame, false );
		imagesavealpha( $scaledEyeFrame, true );
		imagecopyresampled( $scaledEyeFrame, $eyeFrame, 0, 0, 0, 0, $scaledEyeFrameWidth, $scaledEyeFrameHeight, $eyeFrameWidth, $eyeFrameHeight );
		imagefilter( $scaledEyeFrame, IMG_FILTER_COLORIZE, $qr_eye_frame_color['r'], $qr_eye_frame_color['g'], $qr_eye_frame_color['b'], 0 );

			    // Eyeball processing
		$eyeImage = !empty( $eye_image ) ? imagecreatefrompng( $eye_image ) : imagecreatefrompng( CQRCGEN_ADMIN_DIR . '/assets/qrcode/eye-balls/ball0.png' );
		imagefilter( $eyeImage, IMG_FILTER_COLORIZE, $qr_eye_rgb['r'], $qr_eye_rgb['g'], $qr_eye_rgb['b'], 0 );
		$eyeImageWidth = imagesx( $eyeImage );
		$eyeImageHeight = imagesy( $eyeImage );

			    // Define the rotation values for each eye frame image name
			    $eyeRotations = [0, 0, 0]; // Default rotations
			    if ( !empty( $eye_frame_image ) ) {
			    	$eye_name = basename( $eye_frame_image );
			    	switch ( $eye_name ) {
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
			    }

			    // Define positions and rotation for the eyes (top-left, top-right, bottom-left).
			    $eyePositions = array(
			    	array(
			    		'x' => 60,
			    		'y' => 60,
			    		'rotations' => $eyeRotations[0],
			    	),
			    	array(
			    		'x' => $qr_width - $scaledEyeFrameWidth - 60,
			    		'y' => 60,
			    		'rotations' => $eyeRotations[1],
			    	),
			    	array(
			    		'x' => 60,
			    		'y' => $qr_height - $scaledEyeFrameHeight - 60,
			    		'rotations' => $eyeRotations[2],
			    	),
			    );

			    // Overlay the eye frames and eyeballs onto the QR code.
			    foreach ( $eyePositions as $position ) {
			        // Rotate the eye frame
			    	$rotatedEyeFrame = imagerotate( $scaledEyeFrame, $position['rotations'], 0 );
			    	$rotatedEyeFrameWidth = imagesx( $rotatedEyeFrame );
			    	$rotatedEyeFrameHeight = imagesy( $rotatedEyeFrame );

			        // Overlay the rotated eye frame onto the QR code
			    	imagecopy( $qr_image_resource, $rotatedEyeFrame, $position['x'], $position['y'], 0, 0, $rotatedEyeFrameWidth, $rotatedEyeFrameHeight );

			        // Overlay the eyeball
			    	$eyeBallX = $position['x'] + ( $rotatedEyeFrameWidth - $eyeImageWidth ) / 2;
			    	$eyeBallY = $position['y'] + ( $rotatedEyeFrameHeight - $eyeImageHeight ) / 2;
			    	imagecopy( $qr_image_resource, $eyeImage, (int)$eyeBallX, (int)$eyeBallY, 0, 0, $eyeImageWidth, $eyeImageHeight );
			    }

			    // Frame image processing
			    $frame_image_resource = '';

			    if ( ! empty( $frame_image ) ) {
					// Load the background frame image.
			    	$frame_image_resource = imagecreatefrompng( $frame_image );
			    }
			    else {
			    	$frame_image          = CQRCGEN_ADMIN_DIR . '/assets/qrcode/frames/default.png';
			    	$frame_image_resource = imagecreatefrompng( $frame_image );
			    }

				// Get the dimensions of the frame image.
			    $frame_width  = imagesx( $frame_image_resource );
			    $frame_height = imagesy( $frame_image_resource );

				// Calculate the scale factor for the QR code to fit within the frame.
			    $qr_scale = min( $frame_width, $frame_height ) * 0.9 / max( $qr_width, $qr_height );

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
			    	case 'box-top.png':
			    	$padding_top = 300;
			    	break;
			    	case 'box-top-6.png':
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
			    $resized_qr_image = imagescale( $qr_image_resource, (int) $scaled_qr_width, (int) $scaled_qr_height );

				// Create a new image to hold the merged result (frame with QR code).
			    $merged_image_resource = imagecreatetruecolor( $frame_width, $frame_height );

				// Merge the frame image onto the new image.
			    imagecopy( $merged_image_resource, $frame_image_resource, 0, 0, 0, 0, $frame_width, $frame_height );

				// Merge the resized QR code onto the new image (frame).
			    imagecopy(
			    	$merged_image_resource,
			    	$resized_qr_image,
				(int) $qr_x,              // Cast to int
				(int) $qr_y,              // Cast to int
				0,
				0,
				(int) $scaled_qr_width,   // Cast to int
				(int) $scaled_qr_height    // Cast to int
			);

			// Optionally, load and add the logo image.
		    if ( ! empty( $logo_url ) && $logo_url !== 'default' && $logo_url !== 'no' ) {
			// Determine file extension and load the appropriate image.
		    	$file_extension = pathinfo($logo_url, PATHINFO_EXTENSION);
		    	switch (strtolower($file_extension)) {
		    		case 'png':
		    		$logo_image_resource = imagecreatefrompng($logo_url);
		    		break;
		    		case 'jpg':
		    		case 'jpeg':
		    		$logo_image_resource = imagecreatefromjpeg($logo_url);
		    		break;
		    		case 'webp':
		    		$logo_image_resource = imagecreatefromwebp($logo_url);
		    		break;
		    		default:
					return; // Unsupported file type, return early.
				}

			// Get the dimensions of the logo image.
				$logo_width = imagesx( $logo_image_resource );
				$logo_height = imagesy( $logo_image_resource );

			// Default padding values.
				$logo_padding_top = 300;
				$logo_padding_bottom = 50;

			// Adjust padding based on frame type.
				$frame_images = basename( $frame_image );
				switch ( $frame_images ) {
					case 'balloon-bottom.png':
					case 'balloon-bottom-1.png':
					$logo_padding_top = -200;
					break;
					case 'balloon-top.png':
					case 'balloon-top-2.png':
					case 'banner-top.png':
					case 'banner-top-4.png':
					case 'box-top.png':
					case 'box-top-6.png':
					$logo_padding_top = 300;
					break;
					case 'banner-bottom.png':
					case 'banner-bottom-3.png':
					case 'box-bottom.png':
					case 'box-bottom-5.png':
					$logo_padding_top = -200;
					$logo_padding_bottom = 50;
					break;
					case 'focus-8-lite.png':
					case 'focus-lite.png':
					$logo_padding_top = -350;
					break;
					case 'default.png':
					$logo_padding_top = 0;
					$logo_padding_bottom = 0;
					break;
				}

			// Calculate the size and position of the logo relative to the frame with padding.
				$logo_size = min( $frame_width, $frame_height ) / 5;
				$logo_x = ( $frame_width - $logo_size ) / 2;
				$logo_y = ( $frame_height - $logo_size - $logo_padding_top - $logo_padding_bottom ) / 2 + $logo_padding_top;

			// Resize the logo image.
				$resized_logo_image = imagescale( $logo_image_resource, (int) $logo_size, (int) $logo_size );

			// Merge the logo onto the frame image.
				imagecopy(
					$merged_image_resource,
					$resized_logo_image,
					(int) $logo_x,
					(int) $logo_y,
					0,
					0,
					(int) $logo_size,
					(int) $logo_size
				);

			// Free memory.
				imagedestroy( $logo_image_resource );
				imagedestroy( $resized_logo_image );
			}

		$existing_imgdata = $wpdb->get_row( $wpdb->prepare( "SELECT `qr_code`, `created_at`, `updated_at` FROM `$table_name` WHERE `id` = %d", $id ) );// phpcs:ignore

		$old_path_file = !empty( $existing_imgdata->qr_code ) ? basename( $existing_imgdata->qr_code ) : '';
		$date_str = (!empty( $existing_imgdata->updated_at ) && $existing_imgdata->updated_at !== '0000-00-00 00:00:00') 
		? $existing_imgdata->updated_at 
		: $existing_imgdata->created_at;

		if ( !empty( $date_str ) && strtotime( $date_str ) ) {
			$date = new DateTime( $date_str );
			$year = $date->format( 'Y' );
			$month = $date->format( 'm' );
		} else {
			$year = gmdate( 'Y' );
			$month = gmdate( 'm' );
		}

		$old_filename = "wwt-qrcode-$id.png";

		$year_month_dir = "$year/$month";

		$old_file_path = $upload_dir['basedir'] . '/' . $year_month_dir . '/' . ( !empty( $old_path_file ) ? $old_path_file : $old_filename );

		// Remove old image if it exists
		if ( file_exists( $old_file_path ) && $remove_existing == true) {
			wp_delete_file( $old_file_path );
		}

		// Save the final QR code image to a file.
		$old_file_url = $upload_dir['baseurl'] . '/' . $year_month_dir . '/' . $old_filename;
		$filename = 'wwt-qrcode-' . $id . '.png';
		$file     = $upload_dir['basedir'] . '/'.gmdate('Y/m').'/' . $filename;

		if ( $merged_image_resource == '' ) {
			$merged_image_resource = $qr_image_resource;
		}

		imagepng( $merged_image_resource, $file );

	    // Apply filter to prevent intermediate image sizes.
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'cqrc_disable_image_sizes' ) );

	    // Free memory.
		imagedestroy( $qr_image_resource );
		imagedestroy( $frame_image_resource );
		imagedestroy( $resized_qr_image );
		imagedestroy( $merged_image_resource );

		if ( !$is_form_submitted ) {
		    // Get the URL of the uploaded image.
			$attachment_id = media_handle_sideload(['name' => $filename, 'tmp_name' => $file], 0);

		    // Remove the filter after upload.
			remove_filter( 'intermediate_image_sizes_advanced', array( $this, 'cqrc_disable_image_sizes' ) );

			if ( is_wp_error( $attachment_id ) ) {
				return $attachment_id->get_error_message();
			}

		    // Return the URL of the uploaded image.
			return wp_get_attachment_url( $attachment_id );
		}
		return $old_file_url;
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
	 * QRCode Delete Option handle.
	 * @since 1.0.0
	 */
	public function cqrc_handle_qr_code_delete_action() {
		if ( ! empty( $_GET['action'] ) && $_GET['action'] === 'delete' && ! empty( $_GET['id'] ) ) {

        	// Check nonce
			if ( empty( $_REQUEST['_qr_code_nonce_action'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_qr_code_nonce_action'] ) ), 'qr_code_nonce_action' ) ) {
				wp_die( esc_html__( 'Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator' ) );
			}

        	// Sanitize and assign ID
			$id = sanitize_text_field( wp_unslash( $_GET['id'] ) );

        	// Call the global delete function
			cqrc_delete_qr_code_data( $id );

        	// Redirect
			$page = !empty( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
			$redirect_url = add_query_arg( 'page', $page, admin_url( 'admin.php' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Helper method for deleting media posts and associated files
	 * @since 1.0.0
	 */
	private function cqrc_delete_media_posts( $qr_code, $default_logo_name ) {
		global $wpdb;

	    // Step 1: Delete media posts matching the QR code
		$media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `guid` = %s AND `post_type` = 'attachment'", $qr_code ) ); // phpcs:ignore

		if ( $media_posts ) {
			foreach ( $media_posts as $media_post ) {
				wp_delete_post( $media_post->ID, true );
			}
		}

	    // Step 2: Delete logo file and associated media posts
		if ( !empty( $default_logo_name )) {
			$upload_dir = wp_upload_dir();
			$filename = basename( wp_parse_url( $default_logo_name, PHP_URL_PATH ) );
			$file_path = $upload_dir['path'] . '/' . $filename;

			if ( file_exists( $file_path ) ) {
				wp_delete_file( $file_path );

	            // Delete media posts associated with the logo file
				$media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_title` = %s AND `post_type` = 'attachment'", $filename ) ); // phpcs:ignore

				foreach ( $media_posts as $media_post ) {
					wp_delete_post( $media_post->ID, true );
				}
			}
		}
	}
}
