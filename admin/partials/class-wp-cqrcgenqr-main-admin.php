<?php
/**
 * Admin Pages.
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Pages Class
 *
 * Handles all admin functinalities
 *
 * @package Generate QR Code
 * @since 1.0.0
 */
class QrGen_Admin_Pages {

	/**
	 * Construct
	 *
	 * @return void
	 */
	public function __construct() {

		global $wws_model, $wws_scripts;
		$this->model   = $wws_model;
		$this->scripts = $wws_scripts;
	}

	/**
	 * Add Top Level Menu Page
	 *
	 * Runs when the admin_menu hook fires and adds a new
	 * top level admin page and menu item
	 *
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
			array( $this, 'custom_qrcode_generate_form_page' )
		);

    	// Submenu page for "Scanned QR Code".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'Scanned QR Code', 'custom-qrcode-generator' ),
			esc_html__( 'Scanned QR Code', 'custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-users',
			array( $this, 'custom_qrcode_users_page' )
		);

    	// Submenu page for "About Plugin".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'About Plugin', 'custom-qrcode-generator' ),
			esc_html__( 'About Plugin', 'custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-about',
			array( $this, 'custom_qrcode_about_page' )
		);

		// Submenu page for "Export QR Codes".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'Export QR Codes','custom-qrcode-generator' ),
			esc_html__( 'Export QR Codes','custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-export',
			array( $this, 'custom_export_qr_codes')
		);

		// Submenu page for "Import QR Codes".
		add_submenu_page(
			'custom-qrcode-generator',
			esc_html__( 'Import QR Codes','custom-qrcode-generator' ),
			esc_html__( 'Import QR Codes','custom-qrcode-generator' ),
			CQRCGEN_LEVEL,
			'custom-qrcode-import',
			array( $this, 'custom_import_qr_codes')
		);
	}


	/**
	 * Listing Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function cqrc_generator_index_menu_page() {
		require_once CQRCGEN_ADMIN_DIR . '/partials/class-cqrc-index-page.php';
	}
	

	/**
	 * About Numbers of users scanned QR Codes Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function custom_qrcode_users_page() {
		require_once CQRCGEN_ADMIN_DIR . '/partials/custom-qrcode-users.php';
	}

	/**
	 * About Plugin Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function custom_qrcode_about_page() {
		require_once CQRCGEN_ADMIN_DIR . '/partials/custom-qrcode-about-us.php';
	}
	
	/**
	 * Export the QR code table record
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function custom_export_qr_codes() {
		if (isset($_POST['export'])) {
			
			if ( !isset($_REQUEST['csv_export_nonce_field']) || !wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['csv_export_nonce_field'])), 'csv_export_action') ) {
				wp_die( esc_html__('Security check failed.', 'custom-qrcode-generator' ) );
			}

			global $wpdb;
			$table_name = $wpdb->prefix . 'qrcode_generator';
			// Make sure to sanitize the table name
			$table_names = esc_sql($table_name);
			$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_names}"), ARRAY_A); // phpcs:ignore

        	// Use get_results directly since no user inputs are involved
			//$data = $wpdb->get_results($query, ARRAY_A);
			$upload_dir = plugin_dir_path(__FILE__) . 'exports/';

			if (!file_exists($upload_dir)) {
				mkdir($upload_dir, 0755, true); // phpcs:ignore
			}

			$file_name = 'qr_codes_export_' . time() . '.csv';
			$file_path = $upload_dir . $file_name;

			$output = fopen($file_path, 'w'); // phpcs:ignore
			if ( isset( $_POST['columns'] ) ) {
				// Sanitize the input
				$selected_columns = array_map( 'sanitize_text_field', (array) wp_unslash($_POST['columns']) );
			} else {
				$selected_columns = [];
			}

			fputcsv($output, $selected_columns);

			foreach ($data as $row) {
				$filtered_row = [];
				foreach ($selected_columns as $column) {
					$filtered_row[] = isset($row[$column]) ? $row[$column] : '';
				}
				fputcsv($output, $filtered_row);
			}
			fclose($output); // phpcs:ignore

			echo '<h2>' . esc_html__('Export QR Codes', 'custom-qrcode-generator') . '</h2>';
			echo '<p>' . esc_html__('Your CSV file has been created. Click the link below to download:', 'custom-qrcode-generator') . '</p>';
			echo '<a href="' . esc_url(plugins_url('exports/' . $file_name, __FILE__)) . '" class="button button-primary">' . esc_html__('Download CSV', 'custom-qrcode-generator') . '</a>';
			return;
		}
		?>
		<h2><?php esc_html_e('Export QR Codes Table Records', 'custom-qrcode-generator'); ?></h2>
		<form method="post" action="#" enctype="multipart/form-data">
			<?php wp_nonce_field('csv_export_action', 'csv_export_nonce_field'); ?>
			<h3><?php esc_html_e('Select Columns to Export:', 'custom-qrcode-generator'); ?></h3>
			<div id="error-message" style="color: red; display: none; font-size: 12px; padding-bottom: 10px;"></div>
			<label><input type="checkbox" id="select-all" onclick="toggleCheckboxes(this)" checked> Select All</label><br>
			<?php
			$columns = [
				'id', 'user_id', 'name', 'description', 'upload_logo', 'logo_type', 
				'qr_code', 'url', 'total_scans', 'template_name', 'default_logo_name', 
				'frame_name', 'eye_frame_name', 'eye_balls_name', 'qr_eye_color', 
				'qr_eye_frame_color', 'qr_code_color', 'qrcode_level', 'status', 
				'token', 'password', 'created_at', 'updated_at', 'deleted_at'
			];

			foreach ($columns as $column) {
				echo '<label><input type="checkbox" name="columns[]" value="' . esc_attr($column) . '" checked onchange="updateSelectAll()"> ' . esc_html(ucwords(str_replace('_', ' ', $column))) . '</label><br>';
			}
			?>
			<br>
			<input type="submit" name="export" id="export-qrcode-report" class="button button-primary" value="<?php esc_attr_e('Export', 'custom-qrcode-generator'); ?>">
		</form>

		<script>
			function toggleCheckboxes(selectAllCheckbox) {
				const checkboxes = document.querySelectorAll('input[name="columns[]"]');
				checkboxes.forEach(checkbox => {
					checkbox.checked = selectAllCheckbox.checked;
				});
			}

			function updateSelectAll() {
				const checkboxes = document.querySelectorAll('input[name="columns[]"]');
				const selectAllCheckbox = document.getElementById('select-all');
				const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
				selectAllCheckbox.checked = allChecked;
			}

			document.getElementById('export-qrcode-report').addEventListener('click', function(event) {
				const checkboxes = document.querySelectorAll('input[name="columns[]"]');
				const errorMessageDiv = document.getElementById('error-message');
				const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
				
				if (!anyChecked) {
					event.preventDefault();
					errorMessageDiv.textContent = '<?php echo esc_js(__('Please select at least one column to export.', 'custom-qrcode-generator')); ?>';
					errorMessageDiv.style.display = 'block';
				} else {
					errorMessageDiv.style.display = 'none';
				}
			});
		</script>
		<?php
	}

	/**
	 * Import the QR code table record
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */


	public function custom_import_qr_codes() {
    	// Load the WP_Filesystem class
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		global $wp_filesystem;

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

		if (isset($_POST['submit'])) {
			if ( ! isset($_REQUEST['csv_import_nonce_field']) || ! wp_verify_nonce(sanitize_text_field( wp_unslash($_REQUEST['csv_import_nonce_field'])), 'csv_import_action') ) {
				wp_die( esc_html__('Security check failed.', 'custom-qrcode-generator' ) );
			}
			if (!empty($_FILES['csv_file']['tmp_name'])) {
				// Sanitize the tmp_name
				$file_path = filter_var($_FILES['csv_file']['tmp_name'], FILTER_SANITIZE_STRING);

       			// Check if the file is a CSV
				if (isset($_FILES['csv_file']['name'])) {
					$file_type = wp_check_filetype(basename(sanitize_text_field($_FILES['csv_file']['name'])), null);
				}
				// 				$file_type = wp_check_filetype(basename($_FILES['csv_file']['name']), null);
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

				foreach ($lines as $line) {
                if (empty(trim($line))) continue; // Skip empty lines
                $row = str_getcsv($line);
                $id = intval($row[0]);
                $data = array(
                	'user_id' => isset($row[1]) ? intval($row[1]) : null,
                	'name' => isset($row[2]) ? sanitize_text_field($row[2]) : '',
                	'description' => isset($row[3]) ? sanitize_textarea_field($row[3]) : '',
                	'upload_logo' => isset($row[4]) ? sanitize_text_field($row[4]) : '',
                	'logo_type' => isset($row[5]) ? sanitize_text_field($row[5]) : '',
                	'qr_code' => isset($row[6]) ? sanitize_text_field($row[6]) : '',
                	'url' => isset($row[7]) ? esc_url_raw($row[7]) : '',
                	'total_scans' => isset($row[8]) ? intval($row[8]) : 0,
                	'template_name' => isset($row[9]) ? sanitize_text_field($row[9]) : '',
                	'default_logo_name' => isset($row[10]) ? sanitize_text_field($row[10]) : '',
                	'frame_name' => isset($row[11]) ? sanitize_text_field($row[11]) : '',
                	'eye_frame_name' => isset($row[12]) ? sanitize_text_field($row[12]) : '',
                	'eye_balls_name' => isset($row[13]) ? sanitize_text_field($row[13]) : '',
                	'qr_eye_color' => isset($row[14]) ? sanitize_text_field($row[14]) : '',
                	'qr_eye_frame_color' => isset($row[15]) ? sanitize_text_field($row[15]) : '',
                	'qr_code_color' => isset($row[16]) ? sanitize_text_field($row[16]) : '',
                	'qrcode_level' => isset($row[17]) ? sanitize_text_field($row[17]) : '',
                	'status' => isset($row[18]) ? sanitize_text_field($row[18]) : '',
                	'token' => isset($row[19]) ? sanitize_text_field($row[19]) : '',
                	'password' => isset($row[20]) ? sanitize_text_field($row[20]) : '',
                	'updated_at' => current_time('mysql'),
                );

                global $wpdb;
                $existing_entry = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $id)); // phpcs:ignore

                if ($existing_entry) {
                	$wpdb->update($wpdb->prefix . 'qrcode_generator', $data, array('id' => $id)); // phpcs:ignore
                } else {
                	$data['id'] = $id;
                	$img_src = $data['qr_code'];
                	if (isset($img_src) && !empty($img_src)) {
                		$new_file_name =  "cqrc-{$id}";
                		$upload_dir = wp_upload_dir();
                		$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $img_src);

                		if (!file_exists($file_path)) {
                			return new WP_Error('file_not_found', 'The file does not exist.');
                		}

                		$file_info = pathinfo($file_path);
                		$new_file_path = $upload_dir['basedir'] . '/' . $new_file_name . '.' . $file_info['extension'];

                		if (file_exists($new_file_path)) {
                			echo '<h2 style="color:orange;">' . esc_html__('Warning: A QR Code file with the same name already exists, skipping this entry.', 'custom-qrcode-generator') . ' ID: ' . esc_html($id) . '</h2>';
                			continue;
                		}

                		if (!$wp_filesystem->copy($file_path, $new_file_path)) {
                			return new WP_Error('copy_failed', 'Failed to copy the file.');
                		}

                		$attachment = array(
                			'guid'           => $upload_dir['baseurl'] . '/' . $new_file_name . '.' . $file_info['extension'],
                			'post_mime_type' => wp_check_filetype(basename($new_file_path), null)['type'],
                			'post_title'     => sanitize_file_name($new_file_name),
                			'post_content'   => '',
                			'post_status'    => 'inherit'
                		);

                		$attach_id = wp_insert_attachment($attachment, $new_file_path);

                		require_once(ABSPATH . 'wp-admin/includes/image.php');

                		$attach_data = wp_generate_attachment_metadata($attach_id, $new_file_path);
                		wp_update_attachment_metadata($attach_id, $attach_data);
                		$qrcode_new = wp_get_attachment_url($attach_id);

                		if (isset($qrcode_new) && !empty($qrcode_new)) {
                			$data['qr_code'] = $qrcode_new;
                		}
                	}

                	$wpdb->insert($wpdb->prefix . 'qrcode_generator', $data); // phpcs:ignore
                }
            }
            echo '<h2 style="color:green;">' . esc_html__('Import successful!', 'custom-qrcode-generator') . '</h2>';
        }
    }
}


	/**
	 * Listing Page html
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */

	public function custom_qrcode_generate_form_page() {
		require_once CQRCGEN_ADMIN_DIR . '/partials/custom-qrcode-generate-form.php';
	}

	/**
	 * Custom_table_list_enqueue_scripts
	 *
	 * @return void
	 */
	public function custom_table_list_enqueue_scripts() {
		wp_enqueue_style( 'wp-list-table' );
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * Adding Hooks
	 *
	 * @package Generate QR Code
	 * @since 1.0.0
	 */
	public function add_hooks() {
		add_action( 'admin_menu', array( $this, 'genqr_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'custom_table_list_enqueue_scripts' ) );
	}
}