<?php

/**
 * Fired when the plugin is uninstalled.
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 * @package    Cqrc_Generator
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Include the main plugin file to access constants
require_once( plugin_dir_path( __FILE__ ) . 'custom-qrcode-generator.php' );

global $wpdb;
$table_name1 = QRCODE_GENERATOR_TABLE;
$table_name2 = QRCODE_INSIGHTS_TABLE; 

// Combined Query: Join QR Code table with the posts table
$sql = "
SELECT p.ID AS post_id
FROM $table_name1 q
INNER JOIN {$wpdb->posts} p ON p.guid = q.qr_code AND p.post_type = 'attachment'
";

// Execute the query
$media_posts = $wpdb->get_results( $sql );

if ( $media_posts ) {
	foreach ( $media_posts as $media_post ) {
        // true for force delete, false for trash
		wp_delete_post( $media_post->post_id, true );
	}
}

//Drop the Tables
$wpdb->query( "DROP TABLE IF EXISTS $table_name1"); // phpcs:ignore
$wpdb->query( "DROP TABLE IF EXISTS $table_name2"); // phpcs:ignore