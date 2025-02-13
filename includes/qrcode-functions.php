<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * This Code is Generate the view part for Generated QR Code.
 * @link       https://www.worldwebtechnology.com/
 * @since      1.0.0
 *
 * @package    Cqrc_Generator
 */

// Function to get template field options
function cqrc_get_template_field_options() {
    return array(
        'default'          => esc_html__( 'Default', 'custom-qr-code-generator' ),
        'facebook'         => esc_html__( 'Facebook', 'custom-qr-code-generator' ),
        'youtube-circle'   => esc_html__( 'YouTube', 'custom-qr-code-generator' ),
        'twitter-circle'   => esc_html__( 'Twitter', 'custom-qr-code-generator' ),
        'instagram-circle' => esc_html__( 'Instagram', 'custom-qr-code-generator' ),
        'whatsapp-circle'  => esc_html__( 'WhatsApp', 'custom-qr-code-generator' ),
        'gmail'            => esc_html__( 'Gmail', 'custom-qr-code-generator' ),
        'linkedin-circle'  => esc_html__( 'LinkedIn', 'custom-qr-code-generator' ),
    );
}

// Function to get frame field options
function cqrc_get_frame_field_options() {
    return array(
        'default'          => esc_html__( 'Default Frame', 'custom-qr-code-generator' ),
        'balloon-bottom'   => esc_html__( 'Balloon Bottom Scan', 'custom-qr-code-generator' ),
        'balloon-bottom-1' => esc_html__( 'Balloon Bottom Review', 'custom-qr-code-generator' ),
        'balloon-top'      => esc_html__( 'Balloon Top Scan', 'custom-qr-code-generator' ),
        'balloon-top-2'    => esc_html__( 'Balloon Top Review', 'custom-qr-code-generator' ),
        'banner-bottom'    => esc_html__( 'Banner Bottom Scan', 'custom-qr-code-generator' ),
        'banner-bottom-3'  => esc_html__( 'Banner Bottom Review', 'custom-qr-code-generator' ),
        'banner-top'       => esc_html__( 'Banner Top Scan', 'custom-qr-code-generator' ),
        'banner-top-4'     => esc_html__( 'Banner Top Review', 'custom-qr-code-generator' ),
        'box-bottom'       => esc_html__( 'Box Bottom Scan', 'custom-qr-code-generator' ),
        'box-bottom-5'     => esc_html__( 'Box Bottom Review', 'custom-qr-code-generator' ),
        'box-top'          => esc_html__( 'Box Top Scan', 'custom-qr-code-generator' ),
        'box-top-6'        => esc_html__( 'Box Top Review', 'custom-qr-code-generator' ),
        'focus-lite'       => esc_html__( 'Focus Scan', 'custom-qr-code-generator' ),
        'focus-8-lite'     => esc_html__( 'Focus Review', 'custom-qr-code-generator' ),
    );
}

// Function to get eye frame field options
function cqrc_get_eye_frame_field_options() {
    return array(
        'default' => esc_html__( 'Default Frame', 'custom-qr-code-generator' ),
        'frame0'  => esc_html__( 'Square', 'custom-qr-code-generator' ),
        'frame1'  => esc_html__( 'Messanger', 'custom-qr-code-generator' ),
        'frame2'  => esc_html__( 'Glow', 'custom-qr-code-generator' ),
        'frame3'  => esc_html__( 'Glare', 'custom-qr-code-generator' ),
        'frame4'  => esc_html__( 'Square Dots', 'custom-qr-code-generator' ),
        'frame5'  => esc_html__( 'Qutes', 'custom-qr-code-generator' ),
        'frame6'  => esc_html__( 'Square Cut', 'custom-qr-code-generator' ),
        'frame7'  => esc_html__( 'Square Scrached', 'custom-qr-code-generator' ),
        'frame8'  => esc_html__( 'Square lined', 'custom-qr-code-generator' ),
        'frame9'  => esc_html__( 'Square dashed', 'custom-qr-code-generator' ),
        'frame10' => esc_html__( 'Square Bold', 'custom-qr-code-generator' ),
        'frame11' => esc_html__( 'Square Bold Dots', 'custom-qr-code-generator' ),
        'frame12' => esc_html__( 'Circle', 'custom-qr-code-generator' ),
        'frame13' => esc_html__( 'Rectangle', 'custom-qr-code-generator' ),
        'frame14' => esc_html__( 'Outline', 'custom-qr-code-generator' ),
    );
}

// Function to get eye balls field options
function cqrc_get_eye_balls_field_options() {
    return array(
        'default' => esc_html__( 'Default', 'custom-qr-code-generator' ),
        'ball0'   => esc_html__( 'Square', 'custom-qr-code-generator' ),
        'ball1'   => esc_html__( 'Messanger', 'custom-qr-code-generator' ),
        'ball2'   => esc_html__( 'Glow', 'custom-qr-code-generator' ),
        'ball3'   => esc_html__( 'Glare', 'custom-qr-code-generator' ),
        'ball4'   => esc_html__( 'Hexagon', 'custom-qr-code-generator' ),
        'ball5'   => esc_html__( 'Dots', 'custom-qr-code-generator' ),
        'ball6'   => esc_html__( 'Square Cut', 'custom-qr-code-generator' ),
        'ball7'   => esc_html__( 'Square Lining', 'custom-qr-code-generator' ),
        'ball8'   => esc_html__( 'Square Scrached', 'custom-qr-code-generator' ),
        'ball9'   => esc_html__( 'Octa', 'custom-qr-code-generator' ),
        'ball10'  => esc_html__( 'Octa Dots', 'custom-qr-code-generator' ),
        'ball11'  => esc_html__( 'G-Messanger', 'custom-qr-code-generator' ),
        'ball12'  => esc_html__( 'Horizontal Menu', 'custom-qr-code-generator' ),
        'ball13'  => esc_html__( 'Verticle Menu', 'custom-qr-code-generator' ),
        'ball14'  => esc_html__( 'Dot', 'custom-qr-code-generator' ),
        'ball15'  => esc_html__( 'Rectangle Square', 'custom-qr-code-generator' ),
        'ball16'  => esc_html__( 'Outline', 'custom-qr-code-generator' ),
        'ball17'  => esc_html__( 'Diamond', 'custom-qr-code-generator' ),
        'ball18'  => esc_html__( 'Star', 'custom-qr-code-generator' ),
        'ball19'  => esc_html__( 'Verified', 'custom-qr-code-generator' ),
        'ball20'  => esc_html__( 'Octagon', 'custom-qr-code-generator' ),
        'ball21'  => esc_html__( 'Triangle', 'custom-qr-code-generator' )
    );
}

// Function to get logo field options
function cqrc_get_logo_field_options() {
    return array(
        'default'           => esc_html__( 'Default', 'custom-qr-code-generator' ),
        'instagram-circle'  => esc_html__( 'Instagram', 'custom-qr-code-generator' ),
        'facebook'          => esc_html__( 'Facebook', 'custom-qr-code-generator' ),
        'youtube-circle'    => esc_html__( 'YouTube', 'custom-qr-code-generator' ),
        'whatsapp-circle'   => esc_html__( 'WhatsApp', 'custom-qr-code-generator' ),
        'linkedin-circle'   => esc_html__( 'LinkedIn', 'custom-qr-code-generator' ),
        'twitter-circle'    => esc_html__( 'Twitter', 'custom-qr-code-generator' ),
        'gmail'             => esc_html__( 'Gmail', 'custom-qr-code-generator' ),
        'google-play'       => esc_html__( 'Google Play', 'custom-qr-code-generator' ),
        'googleplus-circle' => esc_html__( 'Google Plus', 'custom-qr-code-generator' ),
        'xing-circle'       => esc_html__( 'Xing', 'custom-qr-code-generator' ),
        'google-calendar'   => esc_html__( 'Google Calendar', 'custom-qr-code-generator' ),
        'google-forms'      => esc_html__( 'Google Forms', 'custom-qr-code-generator' ),
        'google-maps'       => esc_html__( 'Google Maps', 'custom-qr-code-generator' ),
        'google-meet'       => esc_html__( 'Google Meet', 'custom-qr-code-generator' ),
        'google-sheets'     => esc_html__( 'Google Sheets', 'custom-qr-code-generator' ),
        'hangouts-meet'     => esc_html__( 'Hangouts Meet', 'custom-qr-code-generator' ),
        'spotify'           => esc_html__( 'Spotify', 'custom-qr-code-generator' ),
        'telegram'          => esc_html__( 'Telegram', 'custom-qr-code-generator' )
    );
}

// Function to get level field options
function cqrc_get_level_field_options() {
    return array(
        'QR_ECLEVEL_H' => esc_html__( 'Level H', 'custom-qr-code-generator' ),
        'QR_ECLEVEL_Q' => esc_html__( 'Level Q', 'custom-qr-code-generator' ),
        'QR_ECLEVEL_M' => esc_html__( 'Level M', 'custom-qr-code-generator' )
    );
}

// Function to display error message
function cqrc_display_error_message() {
    $image_url   = CQRCGEN_PUBLIC_URL . '/assets/image/not-found.png';
    $website_url = 'https://www.worldwebtechnology.com/';
    
    // Prepare the message for display, ensuring it's translatable
    // phpcs:disable
    $message = sprintf(
        '<div style="text-align: center;">
        <img src="%s">
        <p>%s</p>
        <p><a href="%s" class="button button-primary" target="_blank" rel="nofollow noopener">%s</a></p>
        </div>',
        esc_url($image_url),
        esc_html__('The QR Code is no longer accessible or available! For more details, please contact us or visit our website', 'custom-qr-code-generator'),
        esc_url($website_url),
        esc_html__('World Web Technology!', 'custom-qr-code-generator')
    ); 
    // phpcs:enable
    // Display the message and stop execution
    wp_die( wp_kses_post( $message ) );
}

// Function to display password form.
function cqrc_display_password_form( $message ) {
    // Start output buffering
    ob_start();

    // Get the site URL
    $plugins_page_url = site_url();

    // Start the full HTML document
    ?>
    <!DOCTYPE html>
    <html lang="en-US">
    <head>
        <?php  
        // Set the default page title
        add_filter( 'pre_get_document_title', function() { return 'QR Code Generator - Password Form'; }); 
        wp_head();
        ?>
    </head>
    <body <?php body_class('password-page page-password-form'); ?>>
        <div class="container cqrc-site-container">
            <div class="qrcode-form-container">
                <h2><?php esc_html_e( 'Please Enter Secure Password', 'custom-qr-code-generator' ); ?></h2>
                <?php
            // Display the message if it exists
                if ( ! empty( $message ) ) {
                    echo wp_kses_post( $message );
                }
                ?>
                <!-- Password form -->
                <form method="post" action="">
                    <label for="password"><?php esc_html_e( 'Secure Password:', 'custom-qr-code-generator' ); ?></label>
                    <input type="password" name="password" id="password" required>
                    <input class="submit-btn" type="submit" value="<?php esc_html_e( 'Submit', 'custom-qr-code-generator' ); ?>">
                    <a href="<?php echo esc_url( $plugins_page_url ); ?>" class="button"><?php esc_html_e( 'Visit Our Website', 'custom-qr-code-generator' ); ?></a>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
    // Get the contents of the output buffer
    $html = ob_get_clean();

    // Output the full HTML content
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

//Function to display previous error message previous-view.
function cqrc_display_previous_error_message() {
    $previous_image_url = CQRCGEN_PUBLIC_URL . '/assets/image/previous-view.png';
    $website_url        = 'https://www.worldwebtechnology.com/';

    // Prepare the message for display, ensuring it's translatable
    // phpcs:disable
    $message = sprintf(
        '<div style="text-align: center;">
        <img src="%s" class="previous-view-design" width="500px">
        <p>%s</p>
        <p><a href="%s" class="button button-primary" target="_blank" rel="nofollow noopener">%s</a></p>
        </div>',
        esc_url($previous_image_url),
        esc_attr__('Image not found', 'custom-qr-code-generator'),
        esc_html__('The QR code is currently being prepared. Please wait until the process is complete before scanning. For more details, please contact us or visit our website', 'custom-qr-code-generator'),
        esc_url($website_url),
        esc_html__('World Web Technology!', 'custom-qr-code-generator')
    );
    // phpcs:enable
    // Display the message and stop execution
    wp_die( wp_kses_post( $message ) );
}

//Function to get the user location and ip-address.
function cqrc_get_user_location( $ip ) {
    // Check if the IP is private
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $response = wp_remote_get("https://ipinfo.io/{$ip}/json");

        if (is_wp_error($response)) {
            error_log("API Error: " . $response->get_error_message()); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            return null;
        }

        $location_data = json_decode(wp_remote_retrieve_body($response), true);

        // Check if the response has valid data
        if (!empty($location_data['ip'])) {
            $details = [
                'IP' => $location_data['ip'],
                'Hostname' => !empty($location_data['hostname']) ? $location_data['hostname'] : 'N/A',
                'City' => !empty($location_data['city']) ? $location_data['city'] : 'N/A',
                'Region' => !empty($location_data['region']) ? $location_data['region'] : 'N/A',
                'Country' => !empty($location_data['country']) ? $location_data['country'] : 'N/A',
                'Location' => !empty($location_data['loc']) ? $location_data['loc'] : 'N/A',
                'Organization' => !empty($location_data['org']) ? $location_data['org'] : 'N/A',
                'Postal' => !empty($location_data['postal']) ? $location_data['postal'] : 'N/A',
                'Timezone' => !empty($location_data['timezone']) ? $location_data['timezone'] : 'N/A',
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
function cqrc_get_device_type() {
    $user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

    // Check for Mobile devices
    if (preg_match('/Mobile|Android|iPhone|iPad/', $user_agent)) {
        if (preg_match('/iPad|Tablet/', $user_agent)) {
            return 'Tablet';
        }
        return 'Mobile';
    }

    if (preg_match('/Laptop|Macintosh/', $user_agent)) {
        return 'Laptop';
    }

    // Default to Desktop if no match for other categories
    return 'Desktop';
}

// Global delete function
function cqrc_delete_qr_code_data( $id ) {
    global $wpdb;

    // Tables
    $table_name     = esc_sql( QRCODE_GENERATOR_TABLE );
    $insights_table = esc_sql( QRCODE_INSIGHTS_TABLE );

    // Step 1: Retrieve QR Code Data
    $qr_code_row = $wpdb->get_row( $wpdb->prepare( "SELECT `id` AS `qrid`, `qr_code`, `default_logo_name` FROM `{$table_name}` WHERE `id` = %d", $id ) ); // phpcs:ignore
    if ( ! $qr_code_row ) {
        return;
    }

    // Step 2: Delete Media Posts associated with QR Code and Logo
    cqrc_delete_media_posts( $qr_code_row->qr_code, $qr_code_row->default_logo_name );

    // Step 3: Delete related records in QR code insights
    $wpdb->delete( $insights_table, array( 'qrid' => $qr_code_row->qrid ), array( '%d' ) ); // phpcs:ignore

    // Step 4: Delete the QR Code record itself
    $wpdb->delete( $table_name, array( 'ID' => $id ), array( '%d' ) ); // phpcs:ignore
}

// Helper function to delete media posts (QR code image and logo)
function cqrc_delete_media_posts( $qr_code, $default_logo_name ) {
    global $wpdb;

    // Step 1: Delete media posts matching the QR code
    $media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `guid` = %s AND `post_type` = 'attachment'", $qr_code ) ); // phpcs:ignore
    if ( $media_posts ) {
        foreach ( $media_posts as $media_post ) {
            wp_delete_post( $media_post->ID, true );
        }
    }

    // Step 2: Delete logo file and associated media posts
    if ( ! empty( $default_logo_name ) ) {
        $upload_dir = wp_upload_dir();
        $filename = basename( wp_parse_url( $default_logo_name, PHP_URL_PATH ) );
        $file_path = $upload_dir['path'] . '/' . $filename;

        if ( file_exists( $file_path ) ) {
            wp_delete_file( $file_path );

            // Delete media posts associated with the logo file
            $media_posts = $wpdb->get_results( $wpdb->prepare( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_title` = %s AND `post_type` = 'attachment'", $filename ) ); // phpcs:ignore
            foreach ( $media_posts as $media_post ) {
                wp_delete_post( $media_post->ID, true );
            }
        }
    }
}

