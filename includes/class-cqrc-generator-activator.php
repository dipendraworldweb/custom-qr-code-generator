<?php
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
    public static function cqrc_plugin_activate() {
        // Start output buffering to prevent any unexpected output
        ob_start();

        global $wpdb;
        self::cqrc_perform_activation_checks();
        $charset_collate = $wpdb->get_charset_collate();

        // Define table names
        $table_name1 = QRCODE_GENERATOR_TABLE;
        $table_name2 = QRCODE_INSIGHTS_TABLE; 

        // Check if the table exists and create it if not
        if (self::table_exists($table_name1) === false) {
            $sql1 = "CREATE TABLE $table_name1 (
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
                download longtext NULL,
                download_content longtext NULL,
                token varchar(255) NULL,
                password varchar(255) NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY id (id)
            ) $charset_collate;";
			// Include WordPress upgrade functions
			require_once ABSPATH. 'wp-admin/includes/upgrade.php';
            dbDelta($sql1);
        }

        if (self::table_exists($table_name2) === false) {
            $sql2 = "CREATE TABLE $table_name2 (
                id int(10) NOT NULL AUTO_INCREMENT,
                user_ip_address varchar(255) NULL,
                device_type longtext NULL,
                location longtext NULL,
                qrid varchar(255) NULL,
				qr_usage_count varchar(255) NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY id (id)
            ) $charset_collate;";
			// Include WordPress upgrade functions
			require_once ABSPATH. 'wp-admin/includes/upgrade.php';
            dbDelta($sql2);
        }

        // End output buffering
        ob_end_clean();
    }

    private static function cqrc_perform_activation_checks() {
        // Check for PHP version
        if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            self::cqrc_display_activation_error( 'PHP version 7.4 or higher is required. Please upgrade your PHP version.' );
        }

        // Check for WordPress version
        if ( version_compare( get_bloginfo( 'version' ), '5.6', '<' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            self::cqrc_display_activation_error( 'WordPress version 5.6 or higher is required. Please upgrade your WordPress version.' );
        }

        // Check if Multibyte String Library is enabled
        if ( ! extension_loaded( 'mbstring' ) && ! function_exists( 'mb_convert_encoding' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            self::cqrc_display_activation_error( 'Multibyte String Library is not installed/enabled. Please refer to the <a href="https://www.php.net/manual/en/book.mbstring.php" target="_blank">PHP documentation</a>.' );
        }
        // Check if GD/Imagick Image Library is enabled
        else if ( ! extension_loaded( 'gd' ) && ! function_exists( 'gd_info' ) && ! extension_loaded( 'imagick' ) && ! class_exists( 'Imagick' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            self::cqrc_display_activation_error( 'Either GD/Imagick Library is not installed/enabled. Please refer to the <a href="https://www.php.net/manual/en/book.image.php" target="_blank">GD PHP documentation</a> OR <a href="https://www.php.net/manual/en/book.imagick.php" target="_blank">ImageMagick PHP documentation</a>.' );
        }
    }

    private static function cqrc_display_activation_error( $message ) {
        // A helper method to display custom error messages
        $plugins_page_url = admin_url( 'plugins.php' );

        // Prepare the message for translation
        $translated_message = esc_html( $message );

        // Create the HTML for the error message
        $message_html = sprintf(
            '<div style="text-align: center;">
            <p>%s</p>
            <p><a href="%s" class="button button-primary">%s</a></p>
            </div>',
            $translated_message,
            esc_url( $plugins_page_url ),
            esc_html__( 'Return to Plugins Page', 'custom-qr-code-generator' )
        );

        // Display the error message and stop execution
        wp_die( wp_kses_post( $message_html ) );
    }

    // Method to check if the table exists
    private static function table_exists($table_name) {
        global $wpdb;
        if (!empty($table_name)) {
            $escaped_table_name = esc_sql($table_name);
            $query = $wpdb->prepare("SHOW TABLES LIKE {$escaped_table_name}"); // phpcs:ignore
            return $wpdb->get_var($query) === $escaped_table_name; // phpcs:ignore
        }
        return false;
    }
}
