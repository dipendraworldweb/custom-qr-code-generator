<?php

/**
 * The file that defines the core plugin class
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/includes
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Cqrc_Generator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cqrc_Generator_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WWT_QRCODE_GENERATOR_VERSION' ) ) {
			$this->version = WWT_QRCODE_GENERATOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'custom-qr-code-generator';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cqrc_Generator_Loader. Orchestrates the hooks of the plugin.
	 * - Cqrc_Generator_i18n. Defines internationalization functionality.
	 * - Cqrc_Generator_Admin. Defines all hooks for the admin area.
	 * - Cqrc_Generator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once CQRCGEN_INCLUDES_DIR. '/class-cqrc-generator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once CQRCGEN_INCLUDES_DIR. '/class-cqrc-generator-i18n.php';

		/**
		 * Add require files for qrcode template redirect function 
		 */
		require_once CQRCGEN_INCLUDES_DIR. '/qrcode-functions.php';
		
		/**
		 * Added DOM PDF and PHP QRCode Library
		 * through composer.
		 */
		require_once CQRCGEN_DIR. '/vendor/autoload.php';

		/**
		 * Add wordpress list table.
		 */
		if ( ! class_exists( 'WP_List_Table' ) ) {
			require_once ABSPATH. 'wp-admin/includes/class-wp-list-table.php';
		}
		
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH. 'wp-admin/includes/file.php';
		}
		
		// Include required files if not already included.
		require_once ABSPATH. 'wp-admin/includes/media.php';
		require_once ABSPATH. 'wp-admin/includes/file.php';
		require_once ABSPATH. 'wp-admin/includes/image.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once CQRCGEN_ADMIN_DIR. '/class-cqrc-generator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once CQRCGEN_PUBLIC_DIR. '/class-cqrc-generator-public.php';

		$this->loader = new Cqrc_Generator_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Cqrc_Generator_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Cqrc_Generator_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'cqrc_admin_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'cqrc_generator_form_handle' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'cqrc_handle_qr_code_delete_action' );
		$this->loader->add_action( 'wp_ajax_cqrc_handle_qrurl_insert_record', $plugin_admin, 'cqrc_handle_qrurl_insert_record' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Cqrc_Generator_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'cqrc_handle_qr_code_download' );
		$this->loader->add_action( 'init', $plugin_public, 'cqrc_register_qrcode_shortcode' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'cqrc_qrcode_template_redirect' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'cqrc_qrcode_query_vars' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cqrc_Generator_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
