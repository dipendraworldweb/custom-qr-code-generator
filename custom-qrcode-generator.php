<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.worldwebtechnology.com/
 * @since             1.0.0
 * @package           Cqrc_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Custom QRCode Generator
 * Plugin URI:        https://www.worldwebtechnology.com/
 * Description:       The "Custom QRCode Generator" plugin for WordPress is a useful tool that allows users to create QR codes for their website or specific content. With this plugin, users can generate QR codes for various purposes, such as sharing links, promoting products, or providing website information.
 * Version:           1.0.0
 * Author:            World Web Technology
 * Author URI:        https://www.worldwebtechnology.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       custom-qrcode-generator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Plugin dir.
if ( ! defined( 'CQRCGEN_DIR' ) ) {
	define( 'CQRCGEN_DIR', __DIR__ );
}

// Plugin url.
if ( ! defined( 'CQRCGEN_URL' ) ) {
	define( 'CQRCGEN_URL', plugin_dir_url( __FILE__ ) );
}

// Text Domain.
if ( ! defined( 'CQRCGEN_TEXT_DOMAIN' ) ) {
	define( 'CQRCGEN_TEXT_DOMAIN', 'custom-qrcode-generator' );
}

// Plugin admin dir.
if ( ! defined( 'CQRCGEN_ADMIN_DIR' ) ) {
	define( 'CQRCGEN_ADMIN_DIR', CQRCGEN_DIR . '/admin' );
}

// Plugin admin URL.
if ( ! defined( 'CQRCGEN_ADMIN_URL' ) ) {
	define( 'CQRCGEN_ADMIN_URL', CQRCGEN_URL . 'admin' );
}

// Plugin public dir.
if ( ! defined( 'CQRCGEN_PUBLIC_DIR' ) ) {
	define( 'CQRCGEN_PUBLIC_DIR', CQRCGEN_DIR . '/public' );
}

// Plugin public URL.
if ( ! defined( 'CQRCGEN_PUBLIC_URL' ) ) {
	define( 'CQRCGEN_PUBLIC_URL', CQRCGEN_URL . 'public' );
}

// Plugin includes dir.
if ( ! defined( 'CQRCGEN_INCLUDES_DIR' ) ) {
	define( 'CQRCGEN_INCLUDES_DIR', CQRCGEN_DIR . '/includes' );
}

// Plugin includes URL.
if ( ! defined( 'CQRCGEN_INCLUDES_URL' ) ) {
	define( 'CQRCGEN_INCLUDES_URL', CQRCGEN_URL . 'includes' );
}

// Capability in plugin.
if ( ! defined( 'CQRCGEN_LEVEL' ) ) {
	define( 'CQRCGEN_LEVEL', 'manage_options' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CQRC_GENERATOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cqrc-generator-activator.php
 */

add_action('init', 'cqrc_rewrite_rule');
function cqrc_rewrite_rule() {
	add_rewrite_rule(
		'^qrcode_scan/?$',
		'index.php?qrcode_scan=1',
		'top'
	);
}

/**
 * Register custom query variable
 * 
 */
add_filter('query_vars', 'cqrc_query_vars');
function cqrc_query_vars($vars) {
	$vars[] = 'qrcode_scan';
	return $vars;
}

/**
 * Handle the Generated QR Code custom URL request
 */

add_action('template_redirect', 'cqrc_template_redirect');
function cqrc_template_redirect() {
	if (get_query_var('qrcode_scan')) {
		
		// Verify the nonce before processing further
		if (!isset($_REQUEST['qrcode_wpnonce']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['qrcode_wpnonce'])), 'qrcode_scan_nonce')) {
			wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
		}
		
		global $wpdb;
		$table_name = $wpdb->prefix . 'qrcode_insights';
		$generator_table = $wpdb->prefix . 'qrcode_generator';

        // Get decrypted query parameters
		$new_url = isset($_GET['url']) ? sanitize_text_field(wp_unslash($_GET['url'])) : '';
		$new_qrid = isset($_GET['qrid']) ? sanitize_text_field(wp_unslash($_GET['qrid'])) : '';
		$previd = isset($_GET['previd']) ? sanitize_text_field(wp_unslash($_GET['previd'])) : '';
		$token = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
		$message = '';
		$url = hex2bin($new_url);
		$qrid = intval(substr($new_qrid, 0, -3));   
		$user_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
		$device_type = get_device_type();
		$location = get_user_location($user_ip);

		$request_method = isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '';

		if (isset($token) && !empty($token) && isset($_GET['qrcode_wpnonce'])) {
			$plugins_page_url = site_url();

			if ($request_method === 'POST' && !empty($_POST['password'])) {
				$password = sanitize_text_field(wp_unslash($_POST['password']));
				$query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_generator WHERE token = %s AND password = %s", $token, $password); // phpcs:ignore

				if ($wpdb->get_var($query)) { // phpcs:ignore
					$data = array(
						'user_ip_address' => $user_ip,
						'device_type'     => $device_type,
						'location'        => json_encode($location), // phpcs:ignore
						'qrid'            => $qrid,
						'created_at'      => current_time('mysql'),
					);
					$format = array('%s', '%s', '%s', '%d', '%s');
					$existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_insights WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
					if ($existing_record == 0) {
						$wpdb->insert($table_name, $data, $format); // phpcs:ignore
						$scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_insights WHERE qrid = %d", $qrid)); // phpcs:ignore
						$update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d'));	 // phpcs:ignore
					}	
					if ($update !== false) {
						wp_redirect($url);
						exit;
					} else {
						display_error_message();
					}
				} else {
					$message = '<p style="color: red;">Invalid password. Please try again.</p>';
				}
			}
			display_password_form($message);
			die();
		}

		$query = $wpdb->prepare("SELECT token FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $qrid); // phpcs:ignore
		$qrixists = $wpdb->get_var($query); // phpcs:ignore
		
		if (isset($qrixists) && !empty($qrixists)) {
			$plugins_page_url = site_url();
			if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
				$password = sanitize_text_field(wp_unslash($_POST['password']));
				$query = $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_generator WHERE token = %s AND password = %s", $qrixists, $password); // phpcs:ignore

				if ($wpdb->get_var($query)) { // phpcs:ignore
					$data = array(
						'user_ip_address' => $user_ip,
						'device_type'     => $device_type,
						'location'        => json_encode($location), // phpcs:ignore
						'qrid'            => $qrid,
						'created_at'      => current_time('mysql'),
					);
					$format = array('%s', '%s', '%s', '%d', '%s');
					$existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_insights WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
					if ($existing_record == 0) {
						$wpdb->insert($table_name, $data, $format); // phpcs:ignore
						$scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_insights WHERE qrid = %d", $qrid)); // phpcs:ignore
						$update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d'));	// phpcs:ignore
					}
					if ($update !== false) {
						wp_redirect($url);
						exit;
					} else {
						display_error_message();
					}
				} else {
					$message = '<p style="color: red;">Invalid password. Please try again.</p>';
				}
			}
			display_password_form($message);
			die();
		}
		// Check if Previous option disable
		if (isset($previd) && $previd !== '') {
			display_previous_error_message();
		}

        // Check if QRID is empty
		if ($qrid == '') {
			display_error_message();
		}

        // Check if QRID exists in the generator table
		$qrid_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $qrid)); // phpcs:ignore
		
		if ($qrid_exists == 0) {
			display_error_message();
		}

        // Check if the same QRID and IP address exist in the insights table
		$existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_insights WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore

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
				$scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}qrcode_insights WHERE qrid = %d", $qrid)); // phpcs:ignore
                // Update the total_scans in the generator table
				$update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore

				if ($update !== false) {
					wp_redirect($url);
					exit;
				} else {
					display_error_message();
				}
			} else {
				display_error_message();
			}
		} else {
            // Record exists, just redirect
			wp_redirect($url);
			exit;
		}
	}
}


/* Add require files for qrcode template redirect function */
require_once CQRCGEN_INCLUDES_DIR. '/user-functions.php';
require_once CQRCGEN_INCLUDES_DIR. '/error-functions.php';


// Function to display error message
function display_error_message() {
	$image_url = CQRCGEN_PUBLIC_URL . '/not-found.png';
	$website_url = 'https://www.worldwebtechnology.com/';
	$message = sprintf(
		'<div style="text-align: center;">
		<img src="%s" alt="not-found">
		<p>%s</p>
		<p><a href="%s" class="button button-primary" target="_blank" rel="nofollow noopener">%s</a></p>
		</div>',
		esc_url($image_url),
		esc_html__('The QR Code is no longer accessible or available! For more details, please contact us or visit our website', 'custom-qrcode-generator'),
		esc_url($website_url),
		esc_html__('World Web Technology!', 'custom-qrcode-generator')
	);
	wp_die(wp_kses_post($message));
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cqrc-generator-activator.php
 */

function activate_cqrc_generator() {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		// Deactivate plugin if PHP version is less than 7.4.
		deactivate_plugins( plugin_basename( __FILE__ ) );

		// Display a custom error message and provide a link back to the plugins page.
		$plugins_page_url = admin_url( 'plugins.php' );
		$message          = sprintf(
			'<div style="text-align: center;">
			<p>This plugin requires PHP version 7.4 or higher. Please upgrade your PHP version.</p>
			<p><a href="%s" class="button button-primary">Return to Plugins Page</a></p>
			</div>',
			esc_url( $plugins_page_url )
		);
		wp_die( wp_kses_post( $message ) );
	} elseif ( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );

		// Display custom error message for WordPress version.
		$plugins_page_url = admin_url( 'plugins.php' );
		$message          = sprintf(
			'<div style="text-align: center;">
			<p>This plugin requires WordPress version 6.0 or higher. Please upgrade your WordPress version.</p>
			<p><a href="%s" class="button button-primary">Return to Plugins Page</a></p>
			</div>',
			esc_url( $plugins_page_url )
		);
		wp_die( wp_kses_post( $message ) );
	} elseif ( !extension_loaded( 'gd' ) && !function_exists( 'gd_info' )  ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );

		// Display custom error message for GD Image Library.
		$GD_page_url = 'https://www.php.net/manual/en/book.image.php';
		$plugins_page_url = admin_url( 'plugins.php' );
		$message          = sprintf(
			'<div style="text-align: center;">
			<p>GD Library is not installed/enabled. Please refer to the <a href="%s" target="_blank">PHP documentation</a>.</p>
			<p><a href="%s" class="button button-primary">Return to Plugins Page</a></p>
			</div>',
			esc_url( $GD_page_url ),
			esc_url( $plugins_page_url )
		);
		wp_die( wp_kses_post( $message ) );
	} else {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-cqrc-generator-activator.php';
		Cqrc_Generator_Activator::activate();
		cqrc_rewrite_rule();
		flush_rewrite_rules();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cqrc-generator-deactivator.php
 */

function deactivate_cqrc_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cqrc-generator-deactivator.php';
	Cqrc_Generator_Deactivator::deactivate();
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_cqrc_generator' );
register_deactivation_hook( __FILE__, 'deactivate_cqrc_generator' );

/**
 * This Code is Register a shortcode for Generated QR Code.
 */

function cqrc_register_qrcode_shortcode() {
	add_shortcode( 'cqrc_gen_qrcode_view', 'cqrc_shortcode_handler' );
}
add_action( 'init', 'cqrc_register_qrcode_shortcode' );

/**
 * This Code is Generate the view part for Generated QR Code.
 */

require_once CQRCGEN_INCLUDES_DIR. '/qrcode-functions.php';

/**
 * This Code is Get the qrcode details from the database.
 */

function cqrc_generate_qr_code_url( $id ) {
	global $wpdb;
	$generator_table = $wpdb->prefix . 'qrcode_generator';

	if (isset($id)) {
		$qrcode_image_path = $wpdb->get_var($wpdb->prepare("SELECT `qr_code` FROM {$wpdb->prefix}qrcode_generator WHERE id = %d", $id)); // phpcs:ignore
		
		if (!empty($qrcode_image_path)) {
			return $qrcode_image_path;
		}
	}
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require plugin_dir_path( __FILE__ ) . 'includes/class-cqrc-generator.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

function run_cqrc_generator() {

	$plugin = new Cqrc_Generator();
	$plugin->run();
}
run_cqrc_generator();

// includes admin pages.
require_once CQRCGEN_ADMIN_DIR . '/partials/class-wp-cqrcgenqr-main-admin.php';
$cqrcgenqr_admin = new QrGen_Admin_Pages();
$cqrcgenqr_admin->add_hooks();