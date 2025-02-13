<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Define the internationalization functionality
 *
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/includes
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */
class Cqrc_Generator_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function cqrc_load_plugin_textdomain() {
		load_plugin_textdomain(
			'custom-qr-code-generator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
