<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
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
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'custom-qrcode-generator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
