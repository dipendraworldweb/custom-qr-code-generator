<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Verify the nonce before processing further
if ( empty( $_REQUEST['qrcode_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['qrcode_wpnonce'] ) ), 'qrcode_scan_nonce') ) {
    wp_die( esc_html__( 'Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator' ) );
}

global $wpdb;
$generator_table = QRCODE_GENERATOR_TABLE;
$table_name      = QRCODE_INSIGHTS_TABLE; 

// Get decrypted query parameters
$new_url         = ! empty( $_GET['url'] ) ? sanitize_text_field( wp_unslash( $_GET['url'] ) ) : '';
$new_qrid        = ! empty( $_GET['qrid'] ) ? sanitize_text_field( wp_unslash( $_GET['qrid'] ) ) : '';
$previd          = ! empty( $_GET['previd'] ) ? sanitize_text_field( wp_unslash( $_GET['previd'] ) ) : '';
$token           = ! empty( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
$message         = '';
$url             = hex2bin( $new_url );
$qrid            = absint( substr( $new_qrid, 0, -3 ) );   
$user_ip         = ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
$device_type     = cqrc_get_device_type();
$location        = cqrc_get_user_location( $user_ip );
$request_method  = ! empty( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
$current_time = gmdate('Y-m-d H:i:s', current_time('timestamp', true));

if ( ! empty( $token ) && ! empty( sanitize_text_field( wp_unslash( $_GET['qrcode_wpnonce'] ) ) ) ) {
    $plugins_page_url = site_url();

    if ( $request_method === 'POST' && ! empty( $_POST['password'] ) ) {
        $password = sanitize_text_field(wp_unslash($_POST['password']));

        $query = $wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE token = %s AND BINARY password = %s", $token, $password); // phpcs:ignore
        $qr_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE id = %s", $qrid)); // phpcs:ignore

        if ($qr_exists !== 0 && $qr_exists !== '') { // phpcs:ignore
            if ($wpdb->get_var($query)) { // phpcs:ignore
                $data = cqrc_create_qr_data_array($user_ip, $device_type, $location, $qrid, $current_time);
                $format = array('%s', '%s', '%s', '%d', '%s', '%s', '%d');

                // Check if the same QRID and IP address exist in the insights table
                $existing_record = $wpdb->get_var($wpdb->prepare( "SELECT COUNT(*) FROM `$table_name` WHERE `user_ip_address` = %s AND `qrid` = %d", $user_ip, $qrid ) ); // phpcs:ignore

                if ( $existing_record == 0 ) {
                    // If no record exists, insert the new record
                    $inserted = $wpdb->insert( $table_name, $data, $format ); // phpcs:ignore
                    if ( $inserted !== false ) {
                        // Get the total scans for the QRID
                        $scan_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `$table_name` WHERE `qrid` = %d", $qrid ) ); // phpcs:ignore
                        // Update the total_scans in the generator table
                        $update     = $wpdb->update( $generator_table, array( 'total_scans' => $scan_count ), array( 'id' => $qrid ), array( '%d' ), array( '%d' ) ); // phpcs:ignore

                        if ( $update !== false ) {
                            wp_redirect( $url );
                            exit;
                        }
                        else {
                            cqrc_display_error_message();
                        }
                    }
                    else {
                        cqrc_display_error_message();
                    }
                }
                else {
                    // Record exists, increment qr_usage_count
                    $current_usage_count = $wpdb->get_var( $wpdb->prepare( "SELECT `qr_usage_count` FROM `$table_name` WHERE `user_ip_address` = %s AND `qrid` = %d", $user_ip, $qrid ) ); // phpcs:ignore
                    $new_usage_count = absint( $current_usage_count + 1 );

                    // Update the usage count
                    cqrc_update_qr_usage_count( $wpdb, $table_name, $user_ip, $qrid, $new_usage_count, $current_time );

                    // Update the total_scans in the generator table
                    $update = $wpdb->update( $generator_table, array( 'total_scans' => $new_usage_count ), array( 'id' => $qrid ), array( '%d' ), array( '%d' ) ); // phpcs:ignore

                    // Redirect to the URL
                    wp_redirect( $url );
                    exit;
                }
                
            }
            else {
                $message = '<p style="color: red;">Invalid password. Please try again.</p>';
                cqrc_display_password_form( $message );
                exit;
            }
        }
        else{
            cqrc_display_error_message();
            exit;
        }
    }
    cqrc_display_password_form( $message );
    exit;
}

$query    = $wpdb->prepare( "SELECT `token` FROM `$generator_table` WHERE `id` = %d", $qrid ); // phpcs:ignore
$qrixists = $wpdb->get_var( $query ); // phpcs:ignore

if ( ! empty( $qrixists ) ) {
    $message ='';
    $plugins_page_url = site_url();
    if ( $request_method === 'POST' && ! empty( $_POST['password'] ) ) {
        $password = sanitize_text_field(wp_unslash($_POST['password']));
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE token = %s AND BINARY password = %s", $qrixists, $password); // phpcs:ignore

        if ($wpdb->get_var($query)) { // phpcs:ignore
            $data = cqrc_create_qr_data_array($user_ip, $device_type, $location, $qrid, $current_time);
            $format = array('%s', '%s', '%s', '%d', '%s', '%s', '%d');
            // Check if the same QRID and IP address exist in the insights table
            $existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore

            if ( $existing_record == 0 ) {
                // If no record exists, insert the new record
                $inserted = $wpdb->insert($table_name, $data, $format); // phpcs:ignore
                if ($inserted !== false) {
                    // Get the total scans for the QRID
                    $scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
                    // Update the total_scans in the generator table
                    $update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore

                    if ($update !== false) {
                        wp_redirect($url);
                        exit;
                    } else {
                        cqrc_display_error_message();
                    }
                } else {                    
                    cqrc_display_error_message();
                }
            } else {
                // Record exists, increment qr_usage_count
                $current_usage_count = $wpdb->get_var($wpdb->prepare("SELECT qr_usage_count FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
                $new_usage_count = absint($current_usage_count + 1);

                // Update the usage count
                cqrc_update_qr_usage_count($wpdb, $table_name, $user_ip, $qrid, $new_usage_count, $current_time);

                // Update the total_scans in the generator table
                $scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
                $update = $wpdb->update($generator_table, array('total_scans' => $new_usage_count), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore

                // Redirect to the URL
                wp_redirect( $url );
                exit;
            }
        } else {
            $message = '<p style="color: red;">Invalid password. Please try again.</p>';
        }
    }
    if ( !empty ( $message )) {
        cqrc_display_password_form( $message );
        exit;
    }
}

// Check if Previous option disable
if ( ! empty( $previd ) && $previd !== '' ) {
    cqrc_display_previous_error_message();
}

// Check if QRID is empty
if ( $qrid == '' ) {
    cqrc_display_error_message();
}

// Check if QRID exists in the generator table
$qrid_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE id = %d", $qrid)); // phpcs:ignore

if ( $qrid_exists == 0 ) {
    cqrc_display_error_message();
}

// Check if the same QRID and IP address exist in the insights table
$existing_record = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `$table_name` WHERE `user_ip_address` = %s AND `qrid` = %d", $user_ip, $qrid ) ); // phpcs:ignore

if ( $existing_record == 0 ) {
    // If no record exists, insert the new record
    $data = cqrc_create_qr_data_array($user_ip, $device_type, $location, $qrid, $current_time);
    $format = array('%s', '%s', '%s', '%d', '%s', '%s', '%d');

    // Insert data and update scan count
    $inserted = $wpdb->insert($table_name, $data, $format); // phpcs:ignore
    if ($inserted !== false) {
        $current_total_scans = $wpdb->get_var($wpdb->prepare("SELECT total_scans FROM $generator_table WHERE id = %d", $qrid)); // phpcs:ignore

        // If it exists, increment the total_scans by 1
        if ($current_total_scans !== null) {
            $new_total_scans = absint($current_total_scans + 1);
        } else {
            // If it doesn't exist, start with 1
            $new_total_scans = 1;
        }

        // Update the total_scans in the generator table
        $update = $wpdb->update($generator_table, array('total_scans' => $new_total_scans), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore

        if ($update !== false) {
            wp_redirect($url);
            exit;
        } else {
            cqrc_display_error_message();
        }
    } else {
        cqrc_display_error_message();
    }
} else {
    // Record exists, increment qr_usage_count
    $current_usage_count = $wpdb->get_var($wpdb->prepare("SELECT qr_usage_count FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
    $new_usage_count = absint($current_usage_count + 1);

    // Update the usage count
    cqrc_update_qr_usage_count($wpdb, $table_name, $user_ip, $qrid, $new_usage_count, $current_time);

    // Update the total_scans in the generator table
    $current_total_scans = $wpdb->get_var($wpdb->prepare("SELECT total_scans FROM $generator_table WHERE id = %d", $qrid)); // phpcs:ignore
    if ($current_total_scans !== null) {
        $new_total_scans = absint($current_total_scans + 1);
    } else {
        $new_total_scans = 1;
    }

    $update = $wpdb->update($generator_table, array('total_scans' => $new_total_scans), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore

    // Redirect to the URL
    wp_redirect($url);
    exit;
}
// Function to update QR usage count
function cqrc_update_qr_usage_count($wpdb, $table_name, $user_ip, $qrid, $new_usage_count, $current_time) {
    // phpcs:disable
    return $wpdb->update(
        $table_name,
        array(
            'qr_usage_count' => $new_usage_count,
            'updated_at' => $current_time
        ),
        array(
            'user_ip_address' => $user_ip,
            'qrid' => $qrid
        ),
        array(
            '%d',
            '%s'
        ),
        array(
            '%s',
            '%s'
        )
    );
    // phpcs:enable
}
// Function to create QR data array
function cqrc_create_qr_data_array($user_ip, $device_type, $location, $qrid, $current_time) {
    return array(
        'user_ip_address' => $user_ip,
        'device_type'     => $device_type,
        'location'        => wp_json_encode($location),
        'qrid'            => $qrid,
        'created_at'      => $current_time,
        'updated_at'      => $current_time,
        'qr_usage_count'  => 1
    );
}
?>
