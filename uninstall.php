<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 *
 *
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Define the table name.
$table_name_1 = $wpdb->prefix . 'qrcode_generator';
$table_name_2 = $wpdb->prefix . 'qrcode_insights';

// Step 1: Retrieve all QR Code IDs.
$qr_code_rows = $wpdb->get_results( "SELECT ID, qr_code FROM {$wpdb->prefix}qrcode_generator" ); // phpcs:ignore

if ( $qr_code_rows ) {
	foreach ( $qr_code_rows as $qr_code_row ) {
		$qr_code = $qr_code_row->qr_code;

		// Step 2: Find Matching Media Posts.
		$media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment'", $qr_code )); // phpcs:ignore

		// Step 3: Delete All Matching Media Posts.
		if ( $media_posts ) {
			foreach ( $media_posts as $media_post ) {
				// true for force delete, false for trash.
				wp_delete_post( $media_post->ID, true );
			}
		}
	}
}

// Step 4: Drop the Table.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}qrcode_generator" ); // phpcs:ignore
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}qrcode_insights" ); // phpcs:ignore