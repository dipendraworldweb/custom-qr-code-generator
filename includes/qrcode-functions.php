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

add_filter('query_vars', 'cqrc_qrcode_query_vars');
add_action('init', 'cqrc_register_qrcode_shortcode');
add_action('init', 'cqrc_rewrite_rule');
add_action('template_redirect', 'cqrc_qrcode_template_redirect');

/**
 * This Code is Register a shortcode for Generated QR Code.
 */
function cqrc_register_qrcode_shortcode() {
    add_shortcode( 'cqrc_gen_qrcode_view', 'cqrc_qrcode_shortcode_handler' );
}

function cqrc_qrcode_shortcode_handler( $atts ) {
    global $wpdb;
    
    // Fetch existing settings
    $table_name = QRCODE_SETTING_TABLE;
    $settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"));
    $title = !empty($settings->title) ? $settings->title : '';
    $description = !empty($settings->description) ? unserialize($settings->description) : '';
    $download_options = !empty($settings->download) ? $settings->download : '';
    $download_options_array = is_array($download_options) ? $download_options : explode(',', $download_options);
    
    $atts = shortcode_atts( array(
        'id' => '',
    ), $atts, 'cqrc_gen_qrcode_view' );
    
    $download_qr_nonce = wp_create_nonce('download_qr_nonce');
    
    $id = intval( $atts['id'] );
    $download_png_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'png', 'download_qr_nonce' => $download_qr_nonce ), home_url( '/download-qr/' ) ) );
    $download_jpg_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'jpg', 'download_qr_nonce' => $download_qr_nonce ), home_url( '/download-qr/' ) ) );
    $download_pdf_url = esc_url( add_query_arg( array( 'action' => 'download_qr', 'id' => $id, 'type' => 'pdf', 'download_qr_nonce' => $download_qr_nonce ), home_url( '/download-qr/' ) ) );

    $qr_code_url = cqrc_generate_qr_code_url( $id );

    if ( ! $qr_code_url ) {
        return '<p>' . esc_html__( 'QR code not found.', 'custom-qrcode-generator' ) . '</p>';
    }

    ob_start();
    ?>
    <div class="wwt-qrcode-container">
        <div class="qr-code-showing-preview-image-fronted">
            <img src="<?php echo esc_url( $qr_code_url ); ?>" alt="<?php esc_attr_e( 'QR Code', 'custom-qrcode-generator' ); ?>">
        </div>
        <?php if (!empty($title)) : ?>
            <div class="qr-code-main-title">
                <h2 class="title"><?php echo esc_html( $title ); ?></h2>
            </div>
        <?php endif; ?>

        <?php if (!empty($description)) : ?>
            <div class="qr-code-description">
                <?php echo $description; // Output the dynamic description ?>
            </div>
        <?php endif; ?>
        <?php if (in_array('png', $download_options_array) || in_array('jpg', $download_options_array) || in_array('pdf', $download_options_array)) : ?>
        <div class="download-qr-code-column">
            <?php if (in_array('png', $download_options_array)) : ?>
                <a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_png_url ); ?>"><?php esc_html_e( 'Download PNG', 'custom-qrcode-generator' ); ?></a>
            <?php endif; ?>

            <?php if (in_array('jpg', $download_options_array)) : ?>
                <a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_jpg_url ); ?>"><?php esc_html_e( 'Download JPG', 'custom-qrcode-generator' ); ?></a>
            <?php endif; ?>

            <?php if (in_array('pdf', $download_options_array)) : ?>
                <a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_pdf_url ); ?>"><?php esc_html_e( 'Download PDF', 'custom-qrcode-generator' ); ?></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php
return ob_get_clean();
}

/**
 * This Code is Get the qrcode details from the database.
 */

function cqrc_generate_qr_code_url( $id ) {
    global $wpdb;
    $generator_table = QRCODE_GENERATOR_TABLE;

    if (!empty($id)) {
        $qrcode_image_path = $wpdb->get_var($wpdb->prepare("SELECT `qr_code` FROM $generator_table WHERE id = %d", $id)); // phpcs:ignore
        
        if (!empty($qrcode_image_path)) {
            return $qrcode_image_path;
        }
    }
}

/**
 * The code that runs during plugin activation.
 */

function cqrc_rewrite_rule() {
    add_rewrite_rule(
        '^qrcode_scan/?$',
        'index.php?qrcode_scan=1',
        'top'
    );
}

/**
 * Register custom query variable
 */

function cqrc_qrcode_query_vars($vars) {
    $vars[] = 'qrcode_scan';
    return $vars;
}

/**
 * Handle the Generated QR Code custom URL request
 */

function cqrc_qrcode_template_redirect() {
    if (get_query_var('qrcode_scan')) {

        // Verify the nonce before processing further
        if (empty($_REQUEST['qrcode_wpnonce']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['qrcode_wpnonce'])), 'qrcode_scan_nonce')) {
            wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
        }
        
        global $wpdb;
        $generator_table = QRCODE_GENERATOR_TABLE;
        $table_name = QRCODE_INSIGHTS_TABLE; 

        // Get decrypted query parameters
        $new_url = !empty($_GET['url']) ? sanitize_text_field(wp_unslash($_GET['url'])) : '';
        $new_qrid = !empty($_GET['qrid']) ? sanitize_text_field(wp_unslash($_GET['qrid'])) : '';
        $previd = !empty($_GET['previd']) ? sanitize_text_field(wp_unslash($_GET['previd'])) : '';
        $token = !empty($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
        $message = '';
        $url = hex2bin($new_url);
        $qrid = intval(substr($new_qrid, 0, -3));   
        $user_ip = !empty($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
        $device_type = get_device_type();
        $location = get_user_location($user_ip);

        $request_method = !empty($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '';

        if (!empty($token) && !empty($token) && !empty($_GET['qrcode_wpnonce'])) {
            $plugins_page_url = site_url();

            if ($request_method === 'POST' && !empty($_POST['password'])) {
                $password = sanitize_text_field(wp_unslash($_POST['password']));
                $query = $wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE token = %s AND password = %s", $token, $password); // phpcs:ignore

                if ($wpdb->get_var($query)) { // phpcs:ignore
                    $data = array(
                        'user_ip_address' => $user_ip,
                        'device_type'     => $device_type,
                        'location'        => json_encode($location), // phpcs:ignore
                        'qrid'            => $qrid,
                        'created_at'      => current_time('mysql'),
                    );
                    $format = array('%s', '%s', '%s', '%d', '%s');
                    $existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
                    if ($existing_record == 0) {
                        $wpdb->insert($table_name, $data, $format); // phpcs:ignore
                        $scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
                        $update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d'));  // phpcs:ignore
                    }   
                    if ($update !== false) {
                        wp_redirect($url);
                        exit;
                    } else {
                        display_error_message();
                    }
                } else {
                    $message = '<p style="color: red;">Invalid password. Please try again.</p>';
                }
            }
            display_password_form($message);
            wp_die();
        }

        $query = $wpdb->prepare("SELECT token FROM $generator_table WHERE id = %d", $qrid); // phpcs:ignore
        $qrixists = $wpdb->get_var($query); // phpcs:ignore
        
        if (!empty($qrixists) && !empty($qrixists)) {
            $plugins_page_url = site_url();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['password'])) {
                $password = sanitize_text_field(wp_unslash($_POST['password']));
                $query = $wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE token = %s AND password = %s", $qrixists, $password); // phpcs:ignore

                if ($wpdb->get_var($query)) { // phpcs:ignore
                    $data = array(
                        'user_ip_address' => $user_ip,
                        'device_type'     => $device_type,
                        'location'        => json_encode($location), // phpcs:ignore
                        'qrid'            => $qrid,
                        'created_at'      => current_time('mysql'),
                    );
                    $format = array('%s', '%s', '%s', '%d', '%s');
                    $existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore
                    if ($existing_record == 0) {
                        $wpdb->insert($table_name, $data, $format); // phpcs:ignore
                        $scan_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE qrid = %d", $qrid)); // phpcs:ignore
                        $update = $wpdb->update($generator_table, array('total_scans' => $scan_count), array('id' => $qrid), array('%d'), array('%d')); // phpcs:ignore
                    }
                    if ($update !== false) {
                        wp_redirect($url);
                        exit;
                    } else {
                        display_error_message();
                    }
                } else {
                    $message = '<p style="color: red;">Invalid password. Please try again.</p>';
                }
            }
            display_password_form($message);
            wp_die();
        }
        // Check if Previous option disable
        if (!empty($previd) && $previd !== '') {
            display_previous_error_message();
        }

        // Check if QRID is empty
        if ($qrid == '') {
            display_error_message();
        }

        // Check if QRID exists in the generator table
        $qrid_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $generator_table WHERE id = %d", $qrid)); // phpcs:ignore
        
        if ($qrid_exists == 0) {
            display_error_message();
        }

        // Check if the same QRID and IP address exist in the insights table
        $existing_record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip_address = %s AND qrid = %d", $user_ip, $qrid)); // phpcs:ignore

        if ($existing_record == 0) {
            // If no record exists, insert the new record
            $data = array(
                'user_ip_address' => $user_ip,
                'device_type'     => $device_type,
                'location'        => json_encode($location), // phpcs:ignore
                'qrid'            => $qrid,
                'created_at'      => current_time('mysql'),
            );
            $format = array('%s', '%s', '%s', '%d', '%s');

            // Insert data and update scan count
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
                    display_error_message();
                }
            } else {
                display_error_message();
            }
        } else {
            // Record exists, just redirect. For third-party URL redirection, we utilize the wp_redirect method.
            wp_redirect($url);
            exit;
        }
    }
}

// Function to display error message
function display_error_message() {
    $image_url = CQRCGEN_PUBLIC_URL . '/assets/image/not-found.png';
    $website_url = 'https://www.worldwebtechnology.com/';
    
    // Prepare the message for display, ensuring it's translatable
    $message = sprintf(
        '<div style="text-align: center;">
        <img src="%s" alt="%s">
        <p>%s</p>
        <p><a href="%s" class="button button-primary" target="_blank" rel="nofollow noopener">%s</a></p>
        </div>',
        esc_url($image_url),
        esc_attr__('Image not found', 'custom-qrcode-generator'),
        esc_html__('The QR Code is no longer accessible or available! For more details, please contact us or visit our website', 'custom-qrcode-generator'),
        esc_url($website_url),
        esc_html__('World Web Technology!', 'custom-qrcode-generator')
    );

    // Display the message and stop execution
    wp_die(wp_kses_post($message));
}