<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/includes
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */
class Cqrc_Generator_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

    // Define table names
		$table_name1 = $wpdb->prefix . 'qrcode_generator';
		$table_name2 = $wpdb->prefix . 'qrcode_insights'; 

    // SQL to create two tables
		$sql = "CREATE TABLE $table_name1 (
			id int(10) NOT NULL AUTO_INCREMENT,
			user_id varchar(255) NULL,
			name varchar(255) NULL,
			description longtext NULL,
			upload_logo varchar(255) NULL,
			logo_type varchar(255) NULL,
			qr_code TEXT NULL,
			url varchar(255) NULL,
			total_scans varchar(255) NULL,
			template_name varchar(255) NULL,
			default_logo_name varchar(255) NULL,
			frame_name varchar(255) NULL,
			eye_frame_name varchar(255) NULL,
			eye_balls_name varchar(255) NULL,
			qr_eye_color varchar(255) NULL,
			qr_eye_frame_color varchar(255) NULL,
			qr_code_color varchar(255) NULL,
			qrcode_level varchar(255) NULL,
			status varchar(255) NULL,
			token varchar(255) NULL,
			password varchar(255) NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			deleted_at timestamp NULL DEFAULT NULL,
			UNIQUE KEY id (id)
			) $charset_collate;

		CREATE TABLE $table_name2 (
			id int(10) NOT NULL AUTO_INCREMENT,
			user_ip_address varchar(255) NULL,
			device_type longtext NULL,
			location longtext NULL,
			qrid varchar(255) NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			UNIQUE KEY id (id)
		) $charset_collate;";

    // Include WordPress upgrade functions
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta($sql);
	}

}
