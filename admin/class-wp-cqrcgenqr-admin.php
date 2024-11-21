<?php
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
	public function enqueue_styles($hook) {
		wp_enqueue_style( $this->plugin_name, CQRCGEN_ADMIN_URL . '/assets/css/cqrc-generator-admin.css', array(), $this->version, 'all' );
		if ( 'qr-code_page_custom-qrcode-generate-form' == $hook) {
			wp_enqueue_style( $this->plugin_name.'-font-awesome', CQRCGEN_ADMIN_URL . '/assets/css/cqrc-font-awesome.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {
		if ( 'toplevel_page_custom-qrcode-generator' == $hook || 'qr-code_page_custom-qrcode-generate-form' == $hook || 'qr-code_page_custom-qrcode-export' == $hook || 'qr-code_page_custom-qrcode-about' == $hook || 'qr-code_page_custom-qrcode-users' == $hook || 'qr-code_page_custom-qrcode-import' == $hook) {

			wp_enqueue_style( 'wp-list-table' );
			wp_enqueue_media();
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_script( $this->plugin_name . '-admin', CQRCGEN_ADMIN_URL . '/assets/js/cqrc-generator-admin.js', array( 'jquery' ), $this->version, false );
			
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
					'ajax_url' => admin_url('admin-ajax.php'),
					'nonce'    => wp_create_nonce('qr_code_nonce'),
					'downloadUrl' => esc_url(home_url('/download-qr/'))
				)
			);
		}
	}

	/**
	 * Add Top Level Menu Page
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function genqr_admin_menu() {
    	// Main menu page.
		add_menu_page(
			esc_html__( 'QR Code', 'custom-qrcode-generator' ), 
			esc_html__( 'QR Code', 'custom-qrcode-generator' ), 
			CQRCGEN_LEVEL, 
			'custom-qrcode-generator', 
			array( $this, 'cqrc_generator_index_menu_page' ),
			'dashicons-admin-site'
		);

    	// Submenu page for "Add New QR".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'Add New QR', 'custom-qrcode-generator' ),
			esc_html__( 'Add New QR', 'custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-generate-form',
			array( $this, 'cqrc_form_page' )
		);

    	// Submenu page for "QR Code Records".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'QR Code Records', 'custom-qrcode-generator' ),
			esc_html__( 'QR Code Records', 'custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-users-records',
			array( $this, 'cqrc_qrcode_users_page' )
		);

    	// Submenu page for "About Plugin".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'About Plugin', 'custom-qrcode-generator' ),
			esc_html__( 'About Plugin', 'custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-about',
			array( $this, 'cqrc_about_page' )
		);

		// Submenu page for "Export QR Codes".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'Export QR Codes','custom-qrcode-generator' ),
			esc_html__( 'Export QR Codes','custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-export',
			array( $this, 'cqrc_export_qr_codes')
		);

		// Submenu page for "Import QR Codes".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'Import QR Codes','custom-qrcode-generator' ),
			esc_html__( 'Import QR Codes','custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-import',
			array( $this, 'cqrc_import_qr_codes')
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
		if (!empty($_POST['export'])) {

			if ( empty($_REQUEST['csv_export_nonce_field']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['csv_export_nonce_field'])), 'csv_export_action') ) {
				wp_die( esc_html__('Security check failed.', 'custom-qrcode-generator' ) );
			}

			global $wpdb;
			$table_name = QRCODE_GENERATOR_TABLE;
			$table_names = esc_sql($table_name);
			$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_names}"), ARRAY_A); 

        	// Define required columns that must always be checked
			$required_columns = ['id', 'user_id', 'name', 'qr_code', 'url'];

        	// Create the export directory if it doesn't exist
			$upload_dir = plugin_dir_path(__FILE__) . 'exports/';
			wp_mkdir_p($upload_dir); 

			$file_name = 'qr_codes_export_' . time() . '.csv';
			$file_path = $upload_dir . $file_name;

        	// Open file for writing
			$output = fopen($file_path, 'w'); 
			if ( !empty( $_POST['columns'] ) ) {
				$selected_columns = array_map( 'sanitize_text_field', (array) wp_unslash($_POST['columns']) );
			} else {
				$selected_columns = [];
			}

        	// Ensure required columns are always selected
			$selected_columns = array_unique(array_merge($required_columns, $selected_columns));

        	// Sort the selected columns according to the predefined order
			$columns_order = [
				'id', 'user_id', 'name', 'description', 'upload_logo', 'logo_type', 
				'qr_code', 'url', 'total_scans', 'template_name', 'default_logo_name', 
				'frame_name', 'eye_frame_name', 'eye_balls_name', 'qr_eye_color', 
				'qr_eye_frame_color', 'qr_code_color', 'qrcode_level', 'status', 
				'token', 'password', 'created_at', 'updated_at', 'deleted_at'
			];

        	// Filter the columns order to include only the selected columns
			$selected_columns_sorted = array_filter($columns_order, function($column) use ($selected_columns) {
				return in_array($column, $selected_columns);
			});

        	// Write the header row to the CSV
			fputcsv($output, $selected_columns_sorted);

        	// Write the data rows to the CSV
			foreach ($data as $row) {
				$filtered_row = [];
				foreach ($selected_columns_sorted as $column) {
					$filtered_row[] = !empty($row[$column]) ? $row[$column] : '';
				}
				fputcsv($output, $filtered_row);
			}
			fclose($output);

			echo '<h2>' . esc_html__('Export QR Codes', 'custom-qrcode-generator') . '</h2>';
			echo '<p>' . esc_html__('Your CSV file has been created. Click the link below to download:', 'custom-qrcode-generator') . '</p>';
			echo '<a href="' . esc_url(plugins_url('exports/' . $file_name, __FILE__)) . '" class="button button-primary">' . esc_html__('Download CSV', 'custom-qrcode-generator') . '</a>';
			return;
		}
		?>
		<h2><?php esc_html_e('Export QR Codes Table Records', 'custom-qrcode-generator'); ?></h2>
		<form method="post" action="#" enctype="multipart/form-data">
			<?php wp_nonce_field('csv_export_action', 'csv_export_nonce_field'); ?>
			<h3>
				<?php esc_html_e('Select Columns to Export:', 'custom-qrcode-generator'); ?>
			</h3>
			<p style="color: #f00; font-size: 14px; font-style: italic; font-weight:400;">
				<?php esc_html_e('Note: The following fields are required for export and cannot be unchecked: ID, User Id, Name, Qr Code, Url. These fields are necessary for the export to function correctly.', 'custom-qrcode-generator'); ?>
			</p>
			<div id="error-message" style="color: red; display: none; font-size: 12px; padding-bottom: 10px;">
			</div>
			<label>
				<input type="checkbox" id="select-all" onclick="toggleCheckboxes(this)" checked> Select All
			</label><br>
			<?php
			$columns = [
				'id', 'user_id', 'name', 'description', 'upload_logo', 'logo_type', 
				'qr_code', 'url', 'total_scans', 'template_name', 'default_logo_name', 
				'frame_name', 'eye_frame_name', 'eye_balls_name', 'qr_eye_color', 
				'qr_eye_frame_color', 'qr_code_color', 'qrcode_level', 'status', 
				'token', 'password', 'created_at', 'updated_at', 'deleted_at'
			];

	   		// Define the required columns that must always be checked
			$required_columns = ['id', 'user_id', 'name', 'qr_code', 'url'];

			foreach ($columns as $column) {
				$checked = in_array($column, $required_columns) ? 'checked disabled' : 'checked';
				echo '<label><input type="checkbox" name="columns[]" value="' . esc_attr($column) . '" ' . esc_attr($checked) . ' onchange="updateSelectAll()"> ' . esc_html(ucwords(str_replace('_', ' ', $column))) . '</label><br>';
			}
			?>
			<br>
			<input type="submit" name="export" id="export-qrcode-report" class="button button-primary" value="<?php esc_attr_e('Export', 'custom-qrcode-generator'); ?>">
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
		global $wp_filesystem, $wpdb;
		$table_name = QRCODE_GENERATOR_TABLE;

    	// Initialize WP Filesystem
		if ( ! WP_Filesystem() ) {
			echo '<h2 style="color:red;">' . esc_html__('Error: Could not initialize the filesystem.', 'custom-qrcode-generator') . '</h2>';
			return;
		}

		?>
		<h2><?php esc_html_e('Import QR Codes', 'custom-qrcode-generator'); ?></h2>
		<form method="post"  action="#" enctype="multipart/form-data">
			<?php wp_nonce_field('csv_import_action', 'csv_import_nonce_field'); ?>
			<input type="file" name="csv_file" accept=".csv" required>
			<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e('Import', 'custom-qrcode-generator'); ?>">
		</form>
		<?php
		if (!empty($_POST['submit'])) {
			if ( empty($_REQUEST['csv_import_nonce_field']) || ! wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['csv_import_nonce_field'])), 'csv_import_action') ) {
				wp_die( esc_html__('Security check failed.', 'custom-qrcode-generator' ) );
			}
			if (!empty($_FILES['csv_file']['tmp_name'])) {
            	// Sanitize the tmp_name
				$file_path = filter_var($_FILES['csv_file']['tmp_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);


            	// Check if the file is a CSV
				if (!empty($_FILES['csv_file']['name'])) {
					$file_type = wp_check_filetype(basename(sanitize_text_field($_FILES['csv_file']['name'])), null);
				}

				if ($file_type['ext'] !== 'csv') {
					echo '<h2 style="color:red;">' . esc_html__('Error: The uploaded file is not a valid CSV.', 'custom-qrcode-generator') . '</h2>';
					return;
				}

				$file_data = $wp_filesystem->get_contents($file_path);
				$lines = explode("\n", $file_data);
				$header = str_getcsv(array_shift($lines));
				$required_columns = ['id', 'user_id', 'name', 'qr_code', 'url'];

            	// Check for missing required columns
				foreach ($required_columns as $column) {
					if (!in_array($column, $header)) {
						echo '<h2 style="color:red;">' . esc_html__('Error: Missing required column: ', 'custom-qrcode-generator') . esc_html($column) . '</h2>';
						return;
					}
				}

            	// Initialize counters for new, updated, and skipped records
				$new_records = 0;
				$updated_records = 0;
				$skipped_records = 0;
				$skipped_ids = [];
				$skipped_line_numbers = [];

				foreach ($lines as $line_number => $line) {
					if (empty(trim($line))) continue;
					$row = str_getcsv($line);
					$id = intval($row[0]);
					$data = array(
						'user_id' => !empty($row[1]) ? intval($row[1]) : null,
						'name' => !empty($row[2]) ? sanitize_text_field($row[2]) : '',
						'description' => !empty($row[3]) ? sanitize_textarea_field($row[3]) : '',
						'upload_logo' => !empty($row[4]) ? sanitize_text_field($row[4]) : '',
						'logo_type' => !empty($row[5]) ? sanitize_text_field($row[5]) : '',
						'qr_code' => !empty($row[6]) ? sanitize_text_field($row[6]) : '',
						'url' => !empty($row[7]) ? esc_url_raw($row[7]) : '',
						'total_scans' => !empty($row[8]) ? intval($row[8]) : 0,
						'template_name' => !empty($row[9]) ? sanitize_text_field($row[9]) : '',
						'default_logo_name' => !empty($row[10]) ? sanitize_text_field($row[10]) : '',
						'frame_name' => !empty($row[11]) ? sanitize_text_field($row[11]) : '',
						'eye_frame_name' => !empty($row[12]) ? sanitize_text_field($row[12]) : '',
						'eye_balls_name' => !empty($row[13]) ? sanitize_text_field($row[13]) : '',
						'qr_eye_color' => !empty($row[14]) ? sanitize_text_field($row[14]) : '',
						'qr_eye_frame_color' => !empty($row[15]) ? sanitize_text_field($row[15]) : '',
						'qr_code_color' => !empty($row[16]) ? sanitize_text_field($row[16]) : '',
						'qrcode_level' => !empty($row[17]) ? sanitize_text_field($row[17]) : '',
						'status' => !empty($row[18]) ? sanitize_text_field($row[18]) : '',
						'token' => !empty($row[19]) ? sanitize_text_field($row[19]) : '',
						'password' => !empty($row[20]) ? sanitize_text_field($row[20]) : '',
						'updated_at' => current_time('mysql'),
					);

                	// Check if url, name, or description is empty
					if (empty($data['url']) || empty($data['name']) || empty($data['description'])) {
						$skipped_records++;
						$skipped_ids[] = 'Line ' . ($line_number + 2);
						continue;
					}
					
					global $wpdb;
                	$existing_entry = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE id = %d", $id)); // phpcs:ignore

                	if ($existing_entry) {
	                    $wpdb->update($table_name, $data, array('id' => $id)); // phpcs:ignore
	                    $updated_records++;
	                } else {

	                	$data['id'] = '';
	                	$img_src = $data['qr_code'];
	                	if (!empty($img_src)) {
	                		$new_file_name =  "cqrc-{$id}";
	                		$upload_dir = wp_upload_dir();
	                		$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $img_src);

	                		if (!file_exists($file_path)) {
	                			$skipped_records++;
	                			$skipped_ids[] = 'Line ' . ($line_number + 2). ' "Qr Code not exist!"';
	                			continue;
	                		}

	                		$file_info = pathinfo($file_path);
	                		$new_file_path = $upload_dir['basedir'] . '/' . $new_file_name . '.' . $file_info['extension'];

	                		if (!$wp_filesystem->copy($file_path, $new_file_path)) {
	                			$skipped_records++;
	                			$skipped_ids[] = 'Line ' . ($line_number + 2). ' "Qr Code not exist!"';
	                			continue;
	                		}

	                		$attachment = array(
	                			'guid'           => $upload_dir['baseurl'] . '/' . $new_file_name . '.' . $file_info['extension'],
	                			'post_mime_type' => wp_check_filetype(basename($new_file_path), null)['type'],
	                			'post_title'     => sanitize_file_name($new_file_name),
	                			'post_content'   => '',
	                			'post_status'    => 'inherit'
	                		);

	                		$attach_id = wp_insert_attachment($attachment, $new_file_path);
	                		$attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
	                		wp_update_attachment_metadata($attach_id, $attach_data);
	                		$qrcode_new = wp_get_attachment_url($attach_id);

	                		if (!empty($qrcode_new) && !empty($qrcode_new)) {
	                			$data['qr_code'] = $qrcode_new;
	                		}
	                	}
	                    $wpdb->insert($table_name, $data); // phpcs:ignore

	                    $new_records++; 
	                }
	            }

	            // Display the result message
	            echo '<h2 style="color:green;">' . esc_html__('Import successful!', 'custom-qrcode-generator') . '</h2>';
	            echo '<p>' . esc_html__('New Records Added: ', 'custom-qrcode-generator') . esc_attr($new_records) . '</p>';
	            echo '<p>' . esc_html__('Records Updated: ', 'custom-qrcode-generator') . esc_attr($updated_records) . '</p>';
	            echo '<p>' . esc_html__('Records Skipped: ', 'custom-qrcode-generator') . esc_attr($skipped_records) . '</p>';

	            if ($skipped_records > 0) {
	            	echo '<p>' . esc_html__('Lines with Skipped Records: ', 'custom-qrcode-generator') . implode(', ', $skipped_ids) . '</p>'; // phpcs:ignore
	            }
	        }
	    }
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
			wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
		}

		global $wpdb;
		$site_url = site_url();
		$table_name = QRCODE_GENERATOR_TABLE;

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
	private function cqrc_qr_code_generate_process($table_name, $data, $paths, $site_url, $qrid = null) {
		global $wpdb;
		
		// Prepare base URL
		$base_url = $site_url . '/qrcode_scan';

	    // Create nonce for security
		$qrcode_scan_nonce = wp_create_nonce('qrcode_scan_nonce');

	    // If qrid is provided, use it to determine the URL parameters
		if (!empty($qrid)) {
	        $prev_qrcode = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_name ORDER BY id DESC LIMIT 1")); // phpcs:ignore
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
	 * @since    1.0.0
	 */
	public function cqrc_handle_qr_code_generate_action( $url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level ) {

		global $wpdb;
		$merged_image_resource = '';
		$table_name = QRCODE_GENERATOR_TABLE;
		// QR Code Black & White Combination Fixes.
		switch ( $qr_code_color ) {
			case '#ffffff':
			$qr_code_bg_color = 0;
			$qr_eye_color     = '#ffffff';
			break;
			case '#000000':
			$qr_code_bg_color = 16777215;
			$qr_code_color = '#000000';
			break;
			default:
			$qr_code_bg_color = 16777215;
		}

		$qr_eye_frame_color = $this->cqrc_hex_to_rgb( $qr_eye_frame_color );
		
		if ( $qr_code_color !== '' ) {
			$qr_code_colors = $this->cqrc_hex_to_rgb( $qr_code_color );
		}

		$qr_eye_rgb = $this->cqrc_hex_to_rgb( $qr_eye_color );
		// QR Code Level Constants Convertion.
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

		$options = new QROptions([
			'outputType' => QRCode::OUTPUT_IMAGE_PNG,
			'eccLevel'   => $qrcode_level,
			'scale'      => 15,
		]);
		
		ob_start();
		$qrcode = new QRCode($options);
		$qr_image = $qrcode->render($url);
		ob_end_clean();

		$base64_image = str_replace('data:image/png;base64,', '', $qr_image);
		$base64_image = str_replace(' ', '+', $base64_image);
		$image_data = base64_decode($base64_image);

		$upload_dir = wp_upload_dir();
		$file_path = $upload_dir['path'] . '/qrcode.png';

		global $wp_filesystem;

		// Initialize the WP_Filesystem API.
		if ( false === ( $creds = request_filesystem_credentials( site_url() ) ) ) {
			return;
		}

		// Check if we can initialize the filesystem.
		if ( ! WP_Filesystem( $creds ) ) {
			wp_die( esc_html__('Could not initialize WP_Filesystem.', 'custom-qrcode-generator') );
		}

		// Use the WP_Filesystem to write the file.
		if ( ! $wp_filesystem->put_contents( $file_path, $image_data, FS_CHMOD_FILE ) ) {
			wp_die( esc_html__('Failed to save QR code image.', 'custom-qrcode-generator') );
		}

		$qr_image_resource = imagecreatefromstring($image_data);
		if ($qr_image_resource === false) {
			wp_die( esc_html__('Failed to create image from string.', 'custom-qrcode-generator') );
		}

		// Define the new foreground color
		$fgColor = imagecolorallocate($qr_image_resource, $qr_code_colors['r'], $qr_code_colors['g'], $qr_code_colors['b']); // RGB for Blue
		
		// Define white color to replace black in the eye areas
		$whiteColor = imagecolorallocate($qr_image_resource, 255, 255, 255); // RGB for White

		// Iterate through each pixel to change the color from black to your desired color
		$qr_width = imagesx($qr_image_resource);
		$qr_height = imagesy($qr_image_resource);

		if ($qr_width === false || $qr_height === false) {
			wp_die( esc_html__('Failed to get image dimensions.', 'custom-qrcode-generator' ) );
		}

		// Define the eye area positions (top-left, top-right, bottom-left)
		$eyeAreas = [
			['x' => 60, 'y' => 60, 'size' => 7],  // Top-left eye
			['x' => $qr_width - 15 * 12, 'y' => 60, 'size' => 8],  // Top-right eye
			['x' => 60, 'y' => $qr_height - 12 * 15, 'size' => 8],  // Bottom-left eye
		];

		// Iterate through each pixel to change the color
		for ($y = 0; $y < $qr_height; $y++) {
			for ($x = 0; $x < $qr_width; $x++) {
				$currentColor = imagecolorat($qr_image_resource, $x, $y);
				// Skip the eye areas to avoid changing their color
				$inEyeArea = false;
				foreach ($eyeAreas as $eyeArea) {
					if ($x >= $eyeArea['x'] && $x < $eyeArea['x'] + $eyeArea['size'] * 15 &&
						$y >= $eyeArea['y'] && $y < $eyeArea['y'] + $eyeArea['size'] * 15) {
						$inEyeArea = true;
					break;
				}
			}
			if ($inEyeArea && $currentColor == 0) {
				imagesetpixel($qr_image_resource, $x, $y, $whiteColor);
			} elseif (!$inEyeArea && $currentColor == 0) {
				imagesetpixel($qr_image_resource, $x, $y, $fgColor);
			}
		}
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

		//Define the desired scale factor for the eye frames (e.g., 1.5 for 150% size).
	$scaleFactor = 2.1;

		// Calculate the new dimensions of the eye frame.
	$scaledEyeFrameWidth  = $eyeFrameWidth * $scaleFactor;
	$scaledEyeFrameHeight = $eyeFrameHeight * $scaleFactor;

		// Create a new true color image for the scaled eye frame.
	$scaledEyeFrame = imagecreatetruecolor( $scaledEyeFrameWidth, $scaledEyeFrameHeight );

		//Enable transparency for the new image.
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

		//Apply color to the eye frame.
	imagefilter( $scaledEyeFrame, IMG_FILTER_COLORIZE, $qr_eye_frame_color['r'], $qr_eye_frame_color['g'], $qr_eye_frame_color['b'], 0 );

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
		$eyeRotations = array(0, 0, 0);
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
		$eyeballRotations = array(0, 0, 0);
	}

    	// Define positions and rotation for the eyes (top-left, top-right, bottom-left).
	$eyePositions = array(
		array(
			'x' => 60,
			'y' => 60,
			'rotations' => $eyeRotations[0],
			'rotation' => $eyeballRotations[0],
		), 
		array(
			'x' => $qr_width - $scaledEyeFrameWidth - 60,
			'y' => 60,
			'rotations' => $eyeRotations[1],
			'rotation' => $eyeballRotations[1],
		), 
		array(
			'x' => 60,
			'y' => $qr_height - $scaledEyeFrameHeight - 60,
			'rotations' => $eyeRotations[2],
			'rotation' => $eyeballRotations[2],
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
	$resized_qr_image = imagescale( $qr_image_resource, $scaled_qr_width, $scaled_qr_height );

		// Create a new image to hold the merged result (frame with QR code).
	$merged_image_resource = imagecreatetruecolor( $frame_width, $frame_height );

		// Merge the frame image onto the new image.
	imagecopy( $merged_image_resource, $frame_image_resource, 0, 0, 0, 0, $frame_width, $frame_height );

		// Merge the resized QR code onto the new image (frame).
	imagecopy( $merged_image_resource, $resized_qr_image, $qr_x, $qr_y, 0, 0, $scaled_qr_width, $scaled_qr_height );

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
			case 'box-top.png':
			$logo_padding_top    = 300;
			$logo_padding_bottom = 50;
			break;
			case 'box-top-6.png':
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
		$table_name = QRCODE_GENERATOR_TABLE;
			$existing_imgdata = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id)); // phpcs:ignore

			if ( ! $existing_imgdata ) {
				wp_die( esc_html__('No data found for the given ID.', 'custom-qrcode-generator') );
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
					wp_die( esc_html__('Invalid date format in updated_at field.', 'custom-qrcode-generator') );
				}
			} else {
				$date = new DateTime( $created_at );
				$month = $date->format( 'm' );
				$year  = $date->format( 'Y' );
			}
		}
		
		// Save the final QR code image to a file.
		$filename = 'wwt-qrcode-' . ($id ? $id : 1) . '.png';
		if ($merged_image_resource == '') {
			$merged_image_resource = $qr_image_resource;
		}

		imagepng( $merged_image_resource, $filename );
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
		$table_name = QRCODE_GENERATOR_TABLE;
		$site_url = site_url();
		if ( !empty( $_POST['qrcode_url'] ) && !empty( $_POST['qrcode_name'] ) ) {

			if ( empty( $_POST['qr_code_form_data_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qr_code_form_data_nonce'] ) ), 'qr_code_form_data' ) ) {
				return;
			}

			$urls              = !empty( $_POST['qrcode_url'] ) ? sanitize_text_field( wp_unslash($_POST['qrcode_url'])) : '';
			$name              = !empty( $_POST['qrcode_name'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_name'] ) ) : '';
			$description       = !empty( $_POST['qrcode_description'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_description'] ) ) : '';
			$qrid          	   = !empty( $_POST['qrid'] ) ? sanitize_text_field( wp_unslash( $_POST['qrid'] ) ) : '';
			$upload_logo_url   = !empty( $_POST['upload_logo_url'] ) ? sanitize_text_field( wp_unslash( $_POST['upload_logo_url'] ) ) : '';
			$default_logo      = !empty( $_POST['default_logo'] ) ? sanitize_text_field( wp_unslash( $_POST['default_logo'] ) ) : '';
			$default_frame     = !empty( $_POST['default_frame'] ) ? sanitize_text_field( wp_unslash( $_POST['default_frame'] ) ) : '';
			$eye_frame_name    = !empty( $_POST['eye_frame_name'] ) ? sanitize_text_field( wp_unslash( $_POST['eye_frame_name'] ) ) : '';
			$eye_balls_name    = !empty( $_POST['eye_balls_name'] ) ? sanitize_text_field( wp_unslash( $_POST['eye_balls_name'] ) ) : '';
			$template_name     = !empty( $_POST['template_name'] ) ? sanitize_text_field( wp_unslash($_POST['template_name'] )) : '';
			$qrcode_level      = !empty( $_POST['qrcode_level'] ) ? sanitize_text_field( wp_unslash( $_POST['qrcode_level'] ) ) : 'QR_ECLEVEL_H';
			$password      = !empty( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
			$qr_eye_frame_color= !empty( $_POST['qr_eye_frame_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_eye_frame_color'] )) : '';
			$qr_eye_color     = !empty( $_POST['qr_eye_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_eye_color'] )) : '';
			$qr_code_color      = !empty( $_POST['qr_code_color'] ) ? sanitize_hex_color( wp_unslash($_POST['qr_code_color'] )) : '';
			
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
			$logo_option = !empty( $_POST['logo_option'] ) ? sanitize_text_field( wp_unslash( $_POST['logo_option'] ) ) : '';
			
			$u_id = get_current_user_id();
			$uploaded_logo = '';
			if (!empty($qrid)) {
				$qr_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", $qrid ), ARRAY_A ); // phpcs:ignore
				if ( !empty( $qr_data ) ) {
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
				if ( !empty( $upload_logo_url )) {
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

			if ( !empty( $qrid ) ) {
				// Check if $qrid exists in the database.
				$existing_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $qrid ) ); // phpcs:ignore

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

					// Use add_query_arg to build the URL
					$url = add_query_arg( 
						array(
							'url' => bin2hex($urls), 
							'qrid' => $identifier_with_suffix, 
							'qrcode_wpnonce' => $qrcode_scan_nonce
						), 
						$site_url . '/qrcode_scan'
					);

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

					$redirect_url = add_query_arg( 
						array( 
							'page' => 'custom-qrcode-generator'
						), 
						admin_url( 'admin.php' ) 
					);

					// Perform the safe redirect
					wp_redirect( $redirect_url );
					exit;
				}
			}

			// If $qrid is empty or not found in the database, insert new record.
			$data['total_scans'] = '0'; 
			$new_data = $wpdb->insert( $table_name, $data ); // phpcs:ignore

			if ( false === $new_data ) {
				echo 'Database insertion error: ' . esc_html( $wpdb->last_error );
			} else {
				$lastid = $wpdb->insert_id;
				$identifier_with_suffix = $lastid . 'FTA';
				$qrcode_scan_nonce = wp_create_nonce('qrcode_scan_nonce');

				// Use add_query_arg to build the URL
				$url = add_query_arg( 
					array(
						'url' => bin2hex($urls), 
						'qrid' => $identifier_with_suffix, 
						'qrcode_wpnonce' => $qrcode_scan_nonce
					), 
					$site_url . '/qrcode_scan'
				);
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
				$redirect_url = add_query_arg( 
					array( 
						'page' => 'custom-qrcode-generator'
					), 
					admin_url( 'admin.php' ) 
				);

					// Perform the safe redirect
				wp_redirect( $redirect_url );
				exit;
			}
		}
	}
	
	/**
	 * QRCode generate function PHPQRCODE.
	 * @since 1.0.0
	 */
	public function cqrc_generator_create_qr_code( $url, $id, $logo_url, $frame_image, $eye_frame_image, $eye_image, $qr_eye_color, $qr_eye_frame_color, $qr_code_color, $qrcode_level, $password ) {
		global $wpdb;
		$merged_image_resource = '';
		$table_name = QRCODE_GENERATOR_TABLE;
		
		// QR Code Black & White Combination Fixes.
		switch ( $qr_code_color ) {
			case '#ffffff':
			$qr_code_bg_color = 0;
			$qr_eye_color     = '#ffffff';
			break;
			case '#000000':
			$qr_code_bg_color = 16777215;
			$qr_code_color = '#000000';
			break;
			default:
			$qr_code_bg_color = 16777215;
		}

		$qr_eye_frame_color = $this->cqrc_hex_to_rgb( $qr_eye_frame_color );
		
		if ($qr_code_color !== '') {
			$qr_code_colors = $this->cqrc_hex_to_rgb( $qr_code_color );
		}

		$qr_eye_rgb = $this->cqrc_hex_to_rgb( $qr_eye_color );
		// QR Code Level Constants Convertion.
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
			$data = add_query_arg( 'token', $token, $url );
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

		
		$options = new QROptions([
			'outputType' => QRCode::OUTPUT_IMAGE_PNG,
			'eccLevel'   => $qrcode_level,
			'scale'      => 15,
		]);
		
		ob_start();
		$qrcode = new QRCode($options);
		$qr_image = $qrcode->render($data);
		ob_end_clean();

		$base64_image = str_replace('data:image/png;base64,', '', $qr_image);
		$base64_image = str_replace(' ', '+', $base64_image);
		$image_data = base64_decode($base64_image);

		$upload_dir = wp_upload_dir();
		$file_path = $upload_dir['path'] . '/qrcode.png';

		global $wp_filesystem;

		// Initialize the WP_Filesystem API.
		if ( false === ( $creds = request_filesystem_credentials( site_url() ) ) ) {
			return; // Exit if unable to get credentials.
		}

		// Check if we can initialize the filesystem.
		if ( ! WP_Filesystem( $creds ) ) {
			wp_die( esc_html__('Could not initialize WP_Filesystem.', 'custom-qrcode-generator') );
		}

		// Use the WP_Filesystem to write the file.
		if ( ! $wp_filesystem->put_contents( $file_path, $image_data, FS_CHMOD_FILE ) ) {
			wp_die( esc_html__('Failed to save QR code image.', 'custom-qrcode-generator') );
		}

		$qr_image_resource = imagecreatefromstring($image_data);
		if ($qr_image_resource === false) {
			wp_die( esc_html__('Failed to create image from string.', 'custom-qrcode-generator') );
		}

		// Define the new foreground color
		$fgColor = imagecolorallocate($qr_image_resource, $qr_code_colors['r'], $qr_code_colors['g'], $qr_code_colors['b']); // RGB for Blue
		
		// Define white color to replace black in the eye areas
		$whiteColor = imagecolorallocate($qr_image_resource, 255, 255, 255); // RGB for White

		// Iterate through each pixel to change the color from black to your desired color
		$qr_width = imagesx($qr_image_resource);
		$qr_height = imagesy($qr_image_resource);

		if ($qr_width === false || $qr_height === false) {
			wp_die( esc_html__('Failed to get image dimensions.', 'custom-qrcode-generator' ) );
		}

		// Define the eye area positions (top-left, top-right, bottom-left)
		$eyeAreas = [
			['x' => 60, 'y' => 60, 'size' => 7],  // Top-left eye
			['x' => $qr_width - 15 * 12, 'y' => 60, 'size' => 8],  // Top-right eye
			['x' => 60, 'y' => $qr_height - 12 * 15, 'size' => 8],  // Bottom-left eye
		];

		// Iterate through each pixel to change the color
		for ($y = 0; $y < $qr_height; $y++) {
			for ($x = 0; $x < $qr_width; $x++) {
				$currentColor = imagecolorat($qr_image_resource, $x, $y);
				// Skip the eye areas to avoid changing their color
				$inEyeArea = false;
				foreach ($eyeAreas as $eyeArea) {
					if ($x >= $eyeArea['x'] && $x < $eyeArea['x'] + $eyeArea['size'] * 15 &&
						$y >= $eyeArea['y'] && $y < $eyeArea['y'] + $eyeArea['size'] * 15) {
						$inEyeArea = true;
					break;
				}
			}
				if ($inEyeArea && $currentColor == 0) {
					imagesetpixel($qr_image_resource, $x, $y, $whiteColor);
				} elseif (!$inEyeArea && $currentColor == 0) {
					imagesetpixel($qr_image_resource, $x, $y, $fgColor);
				}
			}
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

		//Define the desired scale factor for the eye frames (e.g., 1.5 for 150% size).
		$scaleFactor = 2.1;

		// Calculate the new dimensions of the eye frame.
		$scaledEyeFrameWidth  = $eyeFrameWidth * $scaleFactor;
		$scaledEyeFrameHeight = $eyeFrameHeight * $scaleFactor;

		// Create a new true color image for the scaled eye frame.
		$scaledEyeFrame = imagecreatetruecolor( $scaledEyeFrameWidth, $scaledEyeFrameHeight );

		//Enable transparency for the new image.
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

		//Apply color to the eye frame.
		imagefilter( $scaledEyeFrame, IMG_FILTER_COLORIZE, $qr_eye_frame_color['r'], $qr_eye_frame_color['g'], $qr_eye_frame_color['b'], 0 );

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
			$eyeRotations = array(0, 0, 0);
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
			$eyeballRotations = array(0, 0, 0);
		}
		
    	// Define positions and rotation for the eyes (top-left, top-right, bottom-left).
		$eyePositions = array(
			array(
				'x' => 60,
				'y' => 60,
				'rotations' => $eyeRotations[0],
				'rotation' => $eyeballRotations[0],
			), 
			array(
				'x' => $qr_width - $scaledEyeFrameWidth - 60,
				'y' => 60,
				'rotations' => $eyeRotations[1],
				'rotation' => $eyeballRotations[1],
			), 
			array(
				'x' => 60,
				'y' => $qr_height - $scaledEyeFrameHeight - 60,
				'rotations' => $eyeRotations[2],
				'rotation' => $eyeballRotations[2],
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
				case 'box-top.png':
				$logo_padding_top    = 300;
				$logo_padding_bottom = 50;
				break;
				case 'box-top-6.png':
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

		$existing_imgdata = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );// phpcs:ignore
		if ( ! $existing_imgdata ) {
			wp_die( esc_html__('No data found for the given ID.', 'custom-qrcode-generator' ) );
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
				wp_die( esc_html__('Invalid date format in updated_at field.', 'custom-qrcode-generator') );
			}
		} else {
			$date = new DateTime( $created_at );
			$month = $date->format( 'm' );
			$year  = $date->format( 'Y' );
		}
		$upload_dir       = wp_upload_dir();
		$old_filename     = 'wwt-qrcode-' . $id . '.png';
		
		if (!empty($old_path_file )) {
			$old_file_path    = $upload_dir['basedir'] . '/' . $year . '/' . $month . '/' . $old_path_file;

				// Remove old image if it exists.
			if ( file_exists( $old_file_path ) ) {
				// unlink( $old_file_path );
				wp_delete_file( $old_file_path );
			}
		}

		// Save the final QR code image to a file.
		$filename = 'wwt-qrcode-' . $id . '.png';
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
		if ( !empty($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id']) ) {

			// Check nonce
			if (empty($_REQUEST['_qr_code_nonce_action']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_qr_code_nonce_action'])), 'qr_code_nonce_action')) {
				wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
			}
			
    		// Proceed with deletion logic.
			$id = intval($_GET['id']);
			global $wpdb;
			$table_name = QRCODE_GENERATOR_TABLE;
			$insights_table = QRCODE_INSIGHTS_TABLE; 

    		// Step 1: Retrieve the QR Code Data.
			$qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT qr_code, id AS qrid FROM $table_name WHERE ID = %d", $id ) ); // phpcs:ignore

			$qr_code_rows = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", $id )); // phpcs:ignore
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
					wp_delete_post( $media_post->ID, true );
				}
			}

    		// Step 4: Delete Records from qrcode_insights where qrid matches
			$wpdb->delete( $insights_table, array( 'qrid' => $qrid ), array( '%d' ) );// phpcs:ignore

   			// Step 5: Delete the QR Code Record.
			$wpdb->delete( $table_name, array( 'ID' => $id ), array( '%d' ) );// phpcs:ignore
			$page = !empty( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
			// Redirect after deletion
			wp_redirect(admin_url('admin.php?page=' . $page));
			exit;
		}
	}
}