<?php
/**
 * About QrCode setting page.
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/admin
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
/**
 * About QrCode setting page.
 *
 * @package    Cqrc_Generator
 * @subpackage Cqrc_Generator/admin
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wpdb;
$table_name = QRCODE_SETTING_TABLE;

// Define the default description with translation
$default_description = '<div class="qr-code-description">
<p>' . esc_html__( 'To use the QR code, scan it with a QR code reader or mobile device. Simply point your camera at the code and follow the instructions that appear.', 'custom-qrcode-generator' ) . '</p>
<p>' . esc_html__( 'You can download the QR code in various formats. Choose the one that best suits your needs.', 'custom-qrcode-generator' ) . '</p>
</div>';

// Fetch existing settings
$settings = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name LIMIT 1"));
$title = !empty($settings->title) ? $settings->title : '';
$description = !empty($settings->description) ? unserialize($settings->description) : $default_description;
$download_options = !empty($settings->download) ? $settings->download : '';
?>
<div class="wrap">
	<h1><?php esc_html_e( 'QR Code Setting', 'custom-qrcode-generator' ); ?></h1>
	<div id="response-message"></div>
	<form method="post" action="" enctype="multipart/form-data" id="wwt-qrcode-setting-form">
		<?php wp_nonce_field( 'qr_code_setting_data', 'qr_code_setting_data_nonce' ); ?>
		<div class="row" style="display:flex;">
			<div class="col-md-9" style="width: 100%; display: block">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Form Title', 'custom-qrcode-generator' ); ?></th>
						<td>
							<input type="text" name="title" id="title" value="<?php echo esc_attr($title); ?>" class="regular-text" placeholder="Enter the form title"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Description', 'custom-qrcode-generator' ); ?></th>
						<td>
							<?php
							$settings = array(
								'textarea_name' => 'description',
								'textarea_rows' => 10,
								'tinymce' => true,
								'media_buttons' => false,
								'wpautop' => true,
							);
							wp_editor( $description, 'description', $settings );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Download Options', 'custom-qrcode-generator' ); ?></th>
						<td>
							<label><input type="checkbox" name="download[]" value="png" <?php checked(in_array('png', explode(',', $download_options))); ?>> Download PNG</label><br>
							<label><input type="checkbox" name="download[]" value="jpg" <?php checked(in_array('jpg', explode(',', $download_options))); ?>> Download JPG</label><br>
							<label><input type="checkbox" name="download[]" value="pdf" <?php checked(in_array('pdf', explode(',', $download_options))); ?>> Download PDF</label><br>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="form-buttons">
			<button type="button" class="button-primary" id="submit_qrcode_setting"><?php esc_html_e('Update Setting', 'custom-qrcode-generator'); ?></button>
		</div>
	</form>
</div>