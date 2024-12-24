<?php
/**
 * This Code is Generate the view part for Generated QR Code.
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

// Function to get template field options
function cqrc_get_template_field_options() {
    return array(
        'default'          => __( 'Default', 'custom-qr-code-generator' ),
        'facebook'         => __( 'Facebook', 'custom-qr-code-generator' ),
        'youtube-circle'   => __( 'YouTube', 'custom-qr-code-generator' ),
        'twitter-circle'   => __( 'Twitter', 'custom-qr-code-generator' ),
        'instagram-circle' => __( 'Instagram', 'custom-qr-code-generator' ),
        'whatsapp-circle'  => __( 'WhatsApp', 'custom-qr-code-generator' ),
        'gmail'            => __( 'Gmail', 'custom-qr-code-generator' ),
        'linkedin-circle'  => __( 'LinkedIn', 'custom-qr-code-generator' ),
    );
}

// Function to get frame field options
function cqrc_get_frame_field_options() {
    return array(
        'default'          => __( 'Default Frame', 'custom-qr-code-generator' ),
        'balloon-bottom'   => __( 'Balloon Bottom Scan', 'custom-qr-code-generator' ),
        'balloon-bottom-1' => __( 'Balloon Bottom Review', 'custom-qr-code-generator' ),
        'balloon-top'      => __( 'Balloon Top Scan', 'custom-qr-code-generator' ),
        'balloon-top-2'    => __( 'Balloon Top Review', 'custom-qr-code-generator' ),
        'banner-bottom'    => __( 'Banner Bottom Scan', 'custom-qr-code-generator' ),
        'banner-bottom-3'  => __( 'Banner Bottom Review', 'custom-qr-code-generator' ),
        'banner-top'       => __( 'Banner Top Scan', 'custom-qr-code-generator' ),
        'banner-top-4'     => __( 'Banner Top Review', 'custom-qr-code-generator' ),
        'box-bottom'       => __( 'Box Bottom Scan', 'custom-qr-code-generator' ),
        'box-bottom-5'     => __( 'Box Bottom Review', 'custom-qr-code-generator' ),
        'box-top'          => __( 'Box Top Scan', 'custom-qr-code-generator' ),
        'box-top-6'        => __( 'Box Top Review', 'custom-qr-code-generator' ),
        'focus-lite'       => __( 'Focus Scan', 'custom-qr-code-generator' ),
        'focus-8-lite'     => __( 'Focus Review', 'custom-qr-code-generator' ),
    );
}

// Function to get eye frame field options
function cqrc_get_eye_frame_field_options() {
    return array(
        'default' => __( 'Default Frame', 'custom-qr-code-generator' ),
        'frame0'  => __( 'Square', 'custom-qr-code-generator' ),
        'frame1'  => __( 'Messanger', 'custom-qr-code-generator' ),
        'frame2'  => __( 'Glow', 'custom-qr-code-generator' ),
        'frame3'  => __( 'Glare', 'custom-qr-code-generator' ),
        'frame4'  => __( 'Square Dots', 'custom-qr-code-generator' ),
        'frame5'  => __( 'Qutes', 'custom-qr-code-generator' ),
        'frame6'  => __( 'Square Cut', 'custom-qr-code-generator' ),
        'frame7'  => __( 'Square Scrached', 'custom-qr-code-generator' ),
        'frame8'  => __( 'Square lined', 'custom-qr-code-generator' ),
        'frame9'  => __( 'Square dashed', 'custom-qr-code-generator' ),
        'frame10' => __( 'Square Bold', 'custom-qr-code-generator' ),
        'frame11' => __( 'Square Bold Dots', 'custom-qr-code-generator' ),
        'frame12' => __( 'Circle', 'custom-qr-code-generator' ),
        'frame13' => __( 'Rectangle', 'custom-qr-code-generator' ),
        'frame14' => __( 'Outline', 'custom-qr-code-generator' ),
    );
}

// Function to get eye balls field options
function cqrc_get_eye_balls_field_options() {
    return array(
        'default' => __( 'Default', 'custom-qr-code-generator' ),
        'ball0'   => __( 'Square', 'custom-qr-code-generator' ),
        'ball1'   => __( 'Messanger', 'custom-qr-code-generator' ),
        'ball2'   => __( 'Glow', 'custom-qr-code-generator' ),
        'ball3'   => __( 'Glare', 'custom-qr-code-generator' ),
        'ball4'   => __( 'Hexagon', 'custom-qr-code-generator' ),
        'ball5'   => __( 'Dots', 'custom-qr-code-generator' ),
        'ball6'   => __( 'Square Cut', 'custom-qr-code-generator' ),
        'ball7'   => __( 'Square Lining', 'custom-qr-code-generator' ),
        'ball8'   => __( 'Square Scrached', 'custom-qr-code-generator' ),
        'ball9'   => __( 'Octa', 'custom-qr-code-generator' ),
        'ball10'  => __( 'Octa Dots', 'custom-qr-code-generator' ),
        'ball11'  => __( 'G-Messanger', 'custom-qr-code-generator' ),
        'ball12'  => __( 'Horizontal Menu', 'custom-qr-code-generator' ),
        'ball13'  => __( 'Verticle Menu', 'custom-qr-code-generator' ),
        'ball14'  => __( 'Dot', 'custom-qr-code-generator' ),
        'ball15'  => __( 'Rectangle Square', 'custom-qr-code-generator' ),
        'ball16'  => __( 'Outline', 'custom-qr-code-generator' ),
        'ball17'  => __( 'Diamond', 'custom-qr-code-generator' ),
        'ball18'  => __( 'Star', 'custom-qr-code-generator' ),
        'ball19'  => __( 'Verified', 'custom-qr-code-generator' ),
        'ball20'  => __( 'Octagon', 'custom-qr-code-generator' ),
        'ball21'  => __( 'Triangle', 'custom-qr-code-generator' )
    );
}

// Function to get logo field options
function cqrc_get_logo_field_options() {
    return array(
        'default'           => __( 'Default', 'custom-qr-code-generator' ),
        'instagram-circle'  => __( 'Instagram', 'custom-qr-code-generator' ),
        'facebook'          => __( 'Facebook', 'custom-qr-code-generator' ),
        'youtube-circle'    => __( 'YouTube', 'custom-qr-code-generator' ),
        'whatsapp-circle'   => __( 'WhatsApp', 'custom-qr-code-generator' ),
        'linkedin-circle'   => __( 'LinkedIn', 'custom-qr-code-generator' ),
        'twitter-circle'    => __( 'Twitter', 'custom-qr-code-generator' ),
        'gmail'             => __( 'Gmail', 'custom-qr-code-generator' ),
        'google-play'       => __( 'Google Play', 'custom-qr-code-generator' ),
        'googleplus-circle' => __( 'Google Plus', 'custom-qr-code-generator' ),
        'xing-circle'       => __( 'Xing', 'custom-qr-code-generator' ),
        'google-calendar'   => __( 'Google Calendar', 'custom-qr-code-generator' ),
        'google-forms'      => __( 'Google Forms', 'custom-qr-code-generator' ),
        'google-maps'       => __( 'Google Maps', 'custom-qr-code-generator' ),
        'google-meet'       => __( 'Google Meet', 'custom-qr-code-generator' ),
        'google-sheets'     => __( 'Google Sheets', 'custom-qr-code-generator' ),
        'hangouts-meet'     => __( 'Hangouts Meet', 'custom-qr-code-generator' ),
        'spotify'           => __( 'Spotify', 'custom-qr-code-generator' ),
        'telegram'          => __( 'Telegram', 'custom-qr-code-generator' )
    );
}

// Function to get level field options
function cqrc_get_level_field_options() {
    return array(
        'QR_ECLEVEL_H' => __( 'Level H', 'custom-qr-code-generator' ),
        'QR_ECLEVEL_Q' => __( 'Level Q', 'custom-qr-code-generator' ),
        'QR_ECLEVEL_M' => __( 'Level M', 'custom-qr-code-generator' )
    );
}

// Function to display error message
function cqrc_display_error_message() {
    $image_url = CQRCGEN_PUBLIC_URL . '/assets/image/not-found.png';
    $website_url = 'https://www.worldwebtechnology.com/';
    
    // Prepare the message for display, ensuring it's translatable
    // phpcs:disable
    $message = sprintf(
        '<div style="text-align: center;">
        <img src="%s" alt="%s">
        <p>%s</p>
        <p><a href="%s" class="button button-primary" target="_blank" rel="nofollow noopener">%s</a></p>
        </div>',
        esc_url($image_url),
        esc_attr__('Image not found', 'custom-qr-code-generator'),
        esc_html__('The QR Code is no longer accessible or available! For more details, please contact us or visit our website', 'custom-qr-code-generator'),
        esc_url($website_url),
        esc_html__('World Web Technology!', 'custom-qr-code-generator')
    ); 

    // phpcs:enable

    // Display the message and stop execution
    wp_die(wp_kses_post($message));
}

//Function to display password form.
function cqrc_display_password_form( $message ) {
    $plugins_page_url = site_url();
    $form = '<style>.qrcode-form-container{max-width:400px;margin:50px auto;padding:20px;border:1px solid #ccc;border-radius:5px;background:#f9f9f9;box-shadow:0 2px 10px rgba(0,0,0,.1)}.qrcode-form-container h2{text-align:center;margin-bottom:20px}.qrcode-form-container label{display:block;margin-bottom:8px}.qrcode-form-container input[type=password],.qrcode-form-container input[type=submit]{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px}.qrcode-form-container .error-message{color:red;text-align:center;margin-bottom:15px}.qrcode-form-container a{display:inline-block;text-align:center;margin-top:10px}a.button.button-primary{color:#fff;background-color:#0d6efd;border-color:#0d6efd;padding:.5rem 1rem;font-size:1.25rem;border-radius:.3rem;cursor:pointer;max-width:100%;display:block;text-decoration:none;height: auto;}a.button.button-primary:hover{background-color:#3d3de0}</style>';

    $form .= '<div class="qrcode-form-container">';
    $form .= '<h2>' . esc_html__('Please Enter Secure Password', 'custom-qr-code-generator') . '</h2>';
    
    // Use wp_kses_post to safely output the $message as HTML, ensuring it's safe for translation
    if (!empty($message)) {
        $form .= wp_kses_post($message);
    }
    
    $form .= '<form method="post" action="">
    <label for="password">' . esc_html__('Secure Password:', 'custom-qr-code-generator') . '</label>
    <input type="password" name="password" id="password" required>
    <input type="submit" value="' . esc_html__('Submit', 'custom-qr-code-generator') . '">
    <a href="' . esc_url($plugins_page_url) . '" class="button button-primary">' . esc_html__('Visit Our Website', 'custom-qr-code-generator') . '</a>
    </form>
    </div>';

    // Output the form using echo
    echo ( $form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    
    wp_die();
}

//Function to display previous error message previous-view.
function cqrc_display_previous_error_message() {
    $previous_image_url = CQRCGEN_PUBLIC_URL . '/assets/image/previous-view.png';
    $website_url = 'https://www.worldwebtechnology.com/';

    // Prepare the message for display, ensuring it's translatable
    // phpcs:disable
    $message = sprintf(
        '<div style="text-align: center;">
        <img src="%s" alt="%s" class="previous-view-design" width="500px">
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
    wp_die(wp_kses_post($message));
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
    if (preg_match('/Mobile|Android|iPhone|iPad/', $user_agent)) {
        return 'Mobile';
    }
    return 'Desktop';
}
