<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name:       Custom QR Code Generator
 * Plugin URI:        https://loancalc.worldwebtechnology.com/custom-qr-code-generator-document/
 * Description:       The "Custom QR Code Generator" plugin for WordPress is a useful tool that allows users to create QR codes for their website or specific content. With this plugin, users can generate QR codes for various purposes, such as sharing links, promoting products, or providing website information.
 * Version:           1.0.2
 * Author:            World Web Technology
 * Author URI:        https://www.worldwebtechnology.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       custom-qr-code-generator
 * Domain Path:       /languages
 *
 * @link              https://www.worldwebtechnology.com/
 * @since             1.0.2
 * @package           Cqrc_Generator
 */

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
add_action('init', 'cqrc_add_rewrite_rules');

function cqrc_add_rewrite_rules() {
    add_rewrite_rule(
        '^qrcode_scan/?$',
        'index.php?qrcode_scan=1',
        'top'
    );
}

/**
 * The code that runs during plugin activation.
 */
function cqrc_activate_cqrc_generator() {
    require_once CQRCGEN_INCLUDES_DIR. '/class-cqrc-generator-activator.php';
    Cqrc_Generator_Activator::cqrc_plugin_activate();
    cqrc_add_rewrite_rules();
    flush_rewrite_rules();
}

/**
 * The code that runs during plugin deactivation.
 */
function cqrc_deactivate_cqrc_generator() {
	flush_rewrite_rules();
}

/**
 * The code that runs during plugin uninstallation time.
 */
function cqrc_generator_uninstall() {
    require_once CQRCGEN_INCLUDES_DIR . '/class-cqrc-generator-uninstall.php';
    Cqrc_Generator_Uninstall::cqrc_plugin_uninstall();
}

register_activation_hook( __FILE__, 'cqrc_activate_cqrc_generator' );
register_deactivation_hook( __FILE__, 'cqrc_deactivate_cqrc_generator' );
register_uninstall_hook( __FILE__, 'cqrc_generator_uninstall' );

/**
 * This Code is Generate the view part for Generated QR Code.
 */

/* Add require files for qrcode template redirect function */
require CQRCGEN_INCLUDES_DIR. '/class-cqrc-generator.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.2
 */
function cqrc_generator_run() {
	$plugin = new Cqrc_Generator();
	$plugin->run();
}
cqrc_generator_run();
