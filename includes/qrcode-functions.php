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

function cqrc_shortcode_handler( $atts ) {
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
        <div class="qr-code-description">
            <h2><?php esc_html_e( 'QR Code is Ready!', 'custom-qrcode-generator' ); ?></h2>
            <p><?php esc_html_e( 'To use the QR code, scan it with a QR code reader or mobile device. Simply point your camera at the code and follow the instructions that appear.', 'custom-qrcode-generator' ); ?></p>
            <p><?php esc_html_e( 'You can download the QR code in various formats. Choose the one that best suits your needs.', 'custom-qrcode-generator' ); ?></p>
        </div>
        <div class="download-qr-code-column">
            <a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_png_url ); ?>"><?php esc_html_e( 'Download PNG', 'custom-qrcode-generator' ); ?></a>
            <a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_jpg_url ); ?>"><?php esc_html_e( 'Download JPG', 'custom-qrcode-generator' ); ?></a>
            <a class="button button-primary download-buttons-qrcode download-button" href="<?php echo esc_url( $download_pdf_url ); ?>"><?php esc_html_e( 'Download PDF', 'custom-qrcode-generator' ); ?></a>
        </div>
    </div>
    <?php
    return ob_get_clean();
}