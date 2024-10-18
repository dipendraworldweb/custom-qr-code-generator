<?php
/**
 * Fired when the qrcode scanned and get the user info and device details.
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//Function to get the user location and ip-address.
function get_user_location($ip) {
    // Check if the IP is private
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $response = wp_remote_get("https://ipinfo.io/{$ip}/json");

        if (is_wp_error($response)) {
            error_log("API Error: " . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            return null;
        }

        $location_data = json_decode(wp_remote_retrieve_body($response), true);

        // Check if the response has valid data
        if (isset($location_data['ip'])) {
            $details = [
                'IP' => $location_data['ip'],
                'Hostname' => isset($location_data['hostname']) ? $location_data['hostname'] : 'N/A',
                'City' => isset($location_data['city']) ? $location_data['city'] : 'N/A',
                'Region' => isset($location_data['region']) ? $location_data['region'] : 'N/A',
                'Country' => isset($location_data['country']) ? $location_data['country'] : 'N/A',
                'Location' => isset($location_data['loc']) ? $location_data['loc'] : 'N/A',
                'Organization' => isset($location_data['org']) ? $location_data['org'] : 'N/A',
                'Postal' => isset($location_data['postal']) ? $location_data['postal'] : 'N/A',
                'Timezone' => isset($location_data['timezone']) ? $location_data['timezone'] : 'N/A',
            ];

            // Format the details into a comma-separated string
            return $details;
        } else {
            $details =  [
                'Response' => 'Not valid location data found for the provided IP address.',
                'IP' => $ip,
            ];
            return $details;
        }
    }else {
        $details =  [
            'Response' => 'The provided IP address is private or reserved.',
            'IP' => $ip,
        ];
        return $details;
    }
}

//Function to get the user device and his type.
function get_device_type() {
    // $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
    if (preg_match('/Mobile|Android|iPhone|iPad/', $user_agent)) {
        return 'Mobile';
    }
    return 'Desktop';
}