<?php
/**
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
 *
 * @link              https://www.worldwebtechnology.com/
 * @since             1.0.0
 * @package           Cqrc_Generator
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	wp_die();
}

global $wpdb;

// Plugin dir.
if ( ! defined( 'CQRCGEN_DIR' ) ) {
	define( 'CQRCGEN_DIR', __DIR__ );
}

// Plugin url.
if ( ! defined( 'CQRCGEN_URL' ) ) {
	define( 'CQRCGEN_URL', plugin_dir_url( __FILE__ ) );
}

// Plugin admin dir.
if ( ! defined( 'CQRCGEN_ADMIN_DIR' ) ) {
	define( 'CQRCGEN_ADMIN_DIR', CQRCGEN_DIR . '/admin' );
}

// Plugin admin URL.
if ( ! defined( 'CQRCGEN_ADMIN_URL' ) ) {
	define( 'CQRCGEN_ADMIN_URL', CQRCGEN_URL . 'admin' );
}

// Plugin public URL.
if ( ! defined( 'CQRCGEN_PUBLIC_URL' ) ) {
	define( 'CQRCGEN_PUBLIC_URL', CQRCGEN_URL . 'public' );
}

// Plugin includes dir.
if ( ! defined( 'CQRCGEN_INCLUDES_DIR' ) ) {
	define( 'CQRCGEN_INCLUDES_DIR', CQRCGEN_DIR . '/includes' );
}

// Capability in plugin.
if ( ! defined( 'CQRCGEN_LEVEL' ) ) {
	define( 'CQRCGEN_LEVEL', 'manage_options' );
}

// Define table names as constants
if ( ! defined( 'QRCODE_GENERATOR_TABLE' ) ) {
    define( 'QRCODE_GENERATOR_TABLE', $wpdb->prefix . 'qrcode_generator' );
}

if ( ! defined( 'QRCODE_INSIGHTS_TABLE' ) ) {
    define( 'QRCODE_INSIGHTS_TABLE', $wpdb->prefix . 'qrcode_insights' );
}


/**
 * The code that runs during plugin activation.
 */

function activate_cqrc_generator() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cqrc-generator-activator.php';
    Cqrc_Generator_Activator::activate();
    cqrc_rewrite_rule();
    flush_rewrite_rules();
}

/**
 * The code that runs during plugin deactivation.
 */

function deactivate_cqrc_generator() {
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'activate_cqrc_generator' );
register_deactivation_hook( __FILE__, 'deactivate_cqrc_generator' );

/**
 * This Code is Generate the view part for Generated QR Code.
 */

/* Add require files for qrcode template redirect function */
require plugin_dir_path( __FILE__ ) . 'includes/class-cqrc-generator.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */

function run_cqrc_generator() {
	$plugin = new Cqrc_Generator();
	$plugin->run();
}
run_cqrc_generator();