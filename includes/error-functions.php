<?php
/**
 * Fired when the qrcode scanned and get error and response.
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

//Function to display password form.
function display_password_form($message) {
    $plugins_page_url = site_url();
    $form = '<style>.qrcode-form-container{max-width:400px;margin:50px auto;padding:20px;border:1px solid #ccc;border-radius:5px;background:#f9f9f9;box-shadow:0 2px 10px rgba(0,0,0,.1)}.qrcode-form-container h2{text-align:center;margin-bottom:20px}.qrcode-form-container label{display:block;margin-bottom:8px}.qrcode-form-container input[type=password],.qrcode-form-container input[type=submit]{width:100%;padding:10px;margin-bottom:15px;border:1px solid #ccc;border-radius:4px}.qrcode-form-container .error-message{color:red;text-align:center;margin-bottom:15px}.qrcode-form-container a{display:inline-block;text-align:center;margin-top:10px}a.button.button-primary{color:#fff;background-color:#0d6efd;border-color:#0d6efd;padding:.5rem 1rem;font-size:1.25rem;border-radius:.3rem;cursor:pointer;max-width:100%;display:block;text-decoration:none;height: auto;}a.button.button-primary:hover{background-color:#3d3de0}</style>';

    $form .= '<div class="qrcode-form-container">';
    $form .= '<h2>' . esc_html__('Please Enter Secure Password', 'custom-qrcode-generator') . '</h2>';
    
    // Use wp_kses_post to safely output the $message as HTML, ensuring it's safe for translation
    if (!empty($message)) {
        $form .= wp_kses_post($message);
    }
    
    $form .= '<form method="post" action="">
    <label for="password">' . esc_html__('Secure Password:', 'custom-qrcode-generator') . '</label>
    <input type="password" name="password" id="password" required>
    <input type="submit" value="' . esc_html__('Submit', 'custom-qrcode-generator') . '">
    <a href="' . esc_url($plugins_page_url) . '" class="button button-primary">' . esc_html__('Visit Our Website', 'custom-qrcode-generator') . '</a>
    </form>
    </div>';

    // Output the form using echo
    echo ( $form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    
    wp_die();
}


//Function to display previous error message previous-view.
function display_previous_error_message() {
    $previous_image_url = CQRCGEN_PUBLIC_URL . '/assets/image/previous-view.png';
    $website_url = 'https://www.worldwebtechnology.com/';

    // Prepare the message for display, ensuring it's translatable
    $message = sprintf(
        '<div style="text-align: center;">
        <img src="%s" alt="%s" class="previous-view-design" width="500px">
        <p>%s</p>
        <p><a href="%s" class="button button-primary" target="_blank" rel="nofollow noopener">%s</a></p>
        </div>',
        esc_url($previous_image_url),
        esc_attr__('Image not found', 'custom-qrcode-generator'),
        esc_html__('The QR code is currently being prepared. Please wait until the process is complete before scanning. For more details, please contact us or visit our website', 'custom-qrcode-generator'),
        esc_url($website_url),
        esc_html__('World Web Technology!', 'custom-qrcode-generator')
    );

    // Display the message and stop execution
    wp_die(wp_kses_post($message));
}