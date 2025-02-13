<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/includes
 * @author     World Web Technology <biz@worldwebtechnology.com>
 */
class Cqrc_Generator_Uninstall {
    public static function cqrc_plugin_uninstall() {
        global $wpdb;
        
        // Define the table names
        $table_name1 = esc_sql(QRCODE_GENERATOR_TABLE);
        $table_name2 = esc_sql(QRCODE_INSIGHTS_TABLE);

        // Use prepare for the SQL query
        $sql = "
        SELECT p.ID AS post_id
        FROM $table_name1 q
        INNER JOIN {$wpdb->posts} p ON p.guid = q.qr_code AND p.post_type = %s
        ";

        // Prepare the query
        $prepared_sql = $wpdb->prepare($sql, 'attachment'); // phpcs:ignore
        
        // Get the media posts
        $media_posts = $wpdb->get_results($prepared_sql); // phpcs:ignore
        
        if ($media_posts) {
            foreach ($media_posts as $media_post) {
                // Deleting posts
                wp_delete_post($media_post->post_id, true);
            }
        }

        // Drop the tables
        $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS {$table_name1}")); // phpcs:ignore
        $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS {$table_name2}")); // phpcs:ignore
		wp_cache_flush();
    }
}
