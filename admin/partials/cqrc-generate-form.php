<?php
/**
 * Admin Form.
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

$qr_id = !empty( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

if ( $qr_id > 0 ) {

	// Check nonce
	if (empty($_REQUEST['_qr_code_nonce_action']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_qr_code_nonce_action'])), 'qr_code_nonce_action')) {
		wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qr-code-generator'));
	}

	global $wpdb;
	$table_name = QRCODE_GENERATOR_TABLE;
	$qr_data    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE ID = %d", $qr_id ), ARRAY_A ); // phpcs:ignore

	if ( $qr_data ) {
		$ids                = $qr_data['id'];
		$name               = $qr_data['name'];
		$qrcode             = $qr_data['qr_code'];
		$url                = $qr_data['url'];
		$default_logo_name  = $qr_data['default_logo_name'];
		$template_name      = $qr_data['template_name'];
		$frame_name         = $qr_data['frame_name'];
		$eye_frame_name     = $qr_data['eye_frame_name'];
		$eye_balls_name     = $qr_data['eye_balls_name'];
		$password           = $qr_data['password'];
		$logo_option        = $qr_data['upload_logo'];
		$qr_eye_color       = $qr_data['qr_eye_color'];
		$qr_eye_frame_color = $qr_data['qr_eye_frame_color'];
		$qr_code_color      = $qr_data['qr_code_color'];
		$qrcode_level       = $qr_data['qrcode_level'];
		$download_options   = $qr_data['download'];
		$download_content = json_decode($qr_data['download_content'], true);
		$download_text_png = isset($download_content['png']) ? $download_content['png'] : '';
		$download_text_jpg = isset($download_content['jpg']) ? $download_content['jpg'] : '';
		$download_text_pdf = isset($download_content['pdf']) ? $download_content['pdf'] : '';

		if ( is_serialized( $qr_data['description'] ) ) {
			$qrcode_description = maybe_unserialize( $qr_data['description'] );
		} else {
			$qrcode_description = $qr_data['description'];
		}
	} else {
		wp_die( 
			esc_html_e( 'QR Code not found.', 'custom-qr-code-generator' ) 
		);
	}
} else {
	$ids                = '';
	$name               = '';
	$qrcode_description = '';
	$url                = '';
	$logo_option        = '';
	$default_logo_name  = '';
	$template_name      = '';
	$frame_name         = '';
	$password     		= '';
	$logo_option        = '';
	$qr_eye_color       = '#000000';
	$qr_code_color      = '#000000';
	$qr_eye_frame_color = '#000000';
	$qrcode_level       = '';
	$eye_frame_name     = '';
	$eye_balls_name     = '';
	$download_options   = 'png';
	$download_text_png = 'Download PNG';
	$download_text_jpg = '';
	$download_text_pdf = '';
}

$default_image = CQRCGEN_ADMIN_URL. '/assets/qrcode/dashicon/google-intro.png';
?>
<h1><?php esc_html_e( 'QR Code Generator', 'custom-qr-code-generator' ); ?></h1>
<form method="post" action="" enctype="multipart/form-data" id="wwt-qrcode-generate-form">
	<div id="qrcode-loader" style="display: none;"></div>
	<?php wp_nonce_field( 'qr_code_form_data', 'qr_code_form_data_nonce' ); ?>
	<input type="hidden" value="<?php echo ( $ids ? esc_attr( $ids ) : '' ); ?>" name="qrid">
	<div class="row" style="display:flex;">
		<div class="col-md-9" style="width: 70%; display: block">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'URL', 'custom-qr-code-generator' ); ?><span class="required-lables">*</span></th>
					<td><input type="url" name="qrcode_url" id="qrcode_url" value="<?php echo ( $url ? esc_url( $url ) : '' ); ?>" class="regular-text" placeholder="https://www.google.com/" required/><br><span id="url_error" class="error-message" style="color: red; display: none;"><?php echo esc_html('Maximum length is 75 characters.','custom-qr-code-generator'); ?></span></td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Name', 'custom-qr-code-generator' ); ?><span class="required-lables">*</span></th>
					<td><input type="text" name="qrcode_name" id="qrcode_name" value="<?php echo ( $name ? esc_attr( $name ) : '' ); ?>" class="regular-text" placeholder="Name" required/><br><span id="name_error" class="error-message" style="color: red; display: none;"><?php echo esc_html('Maximum length is 30 characters & Only Allowed Character.','custom-qr-code-generator'); ?></span></td>
				</tr>				
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Description', 'custom-qr-code-generator' ); ?></th>
					<td>
						<?php
						$settings = array(
							'textarea_name' => 'qrcode_description',
							'textarea_rows' => '10',
							'tinymce' => true,
							'media_buttons' => false,
							'wpautop' => false,
						);
						wp_editor( $qrcode_description, 'description', $settings );
						?>
						<span id="description_error" class="error-message" style="color: red; display: none;"><?php echo esc_html('Maximum word count is 100.','custom-qr-code-generator'); ?></span>
					</td>
				</tr>
				<tr valign="top" class="additional-settingss">
					<th scope="row"><?php esc_html_e( 'Default Templates', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div class="select-with-default-template" style="display: flex; gap: 5px;">
							<select id="template_name" name="template_name" class="regular-text">
								<?php
								// Define custom labels for each template option
								$template_field_options = cqrc_get_template_field_options();
								if( ! empty( $template_field_options ) ) {
									// Loop through the options array
									foreach ( $template_field_options as $option_value => $label ) {
										echo '<option value="' . esc_attr( $option_value ) . '" ' . selected( $template_name, $option_value, false ) . '>' . esc_html( $label ) . '</option>';
									}
								}
								?>
							</select>
							<?php // phpcs:disable ?>
							<img id="template_preview" src="" alt="Template Preview" width="30px">
							<?php // phpcs:enable ?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Download Options', 'custom-qr-code-generator' ); ?></th>
					<td>
						<label><input type="checkbox" name="download[]" value="png" <?php checked(in_array('png', explode(',', $download_options))); ?>> <?php esc_html_e( 'Download PNG', 'custom-qr-code-generator' ); ?></label><br>
						<label><input type="checkbox" name="download[]" value="jpg" <?php checked(in_array('jpg', explode(',', $download_options))); ?>> <?php esc_html_e( 'Download JPG', 'custom-qr-code-generator' ); ?></label><br>
						<label><input type="checkbox" name="download[]" value="pdf" <?php checked(in_array('pdf', explode(',', $download_options))); ?>> <?php esc_html_e( 'Download PDF', 'custom-qr-code-generator' ); ?></label><br>
					</td>
				</tr>
				<tr valign="top" class="download-text-png" style="display: none;">
					<th scope="row"><?php esc_html_e( 'Download Button Text - PNG', 'custom-qr-code-generator' ); ?></th>
					<td>
						<input type="text" name="download_text_png" value="<?php echo esc_attr( isset( $download_text_png ) ? $download_text_png : esc_html__( 'Download PNG', 'custom-qr-code-generator' ) ); ?>" class="regular-text" placeholder="Enter text for PNG download button"/><br><span id="download_text_png_error" class="error-message" style="color: red; display: none;"></span>
					</td>
				</tr>
				<tr valign="top" class="download-text-jpg" style="display: none;">
					<th scope="row"><?php esc_html_e( 'Download Button Text - JPG', 'custom-qr-code-generator' ); ?></th>
					<td>
						<input type="text" name="download_text_jpg" value="<?php echo esc_attr( isset( $download_text_jpg ) ? $download_text_jpg : esc_html__( 'Download JPG', 'custom-qr-code-generator' ) ); ?>" class="regular-text" placeholder="Enter text for JPG download button"/><br><span id="download_text_jpg_error" class="error-message" style="color: red; display: none;"></span>
					</td>
				</tr>
				<tr valign="top" class="download-text-pdf" style="display: none;">
					<th scope="row"><?php esc_html_e( 'Download Button Text - PDF', 'custom-qr-code-generator' ); ?></th>
					<td>
						<input type="text" name="download_text_pdf" value="<?php echo esc_attr( isset( $download_text_pdf ) ? $download_text_pdf : esc_html__( 'Download PDF', 'custom-qr-code-generator' ) ); ?>" class="regular-text" placeholder="Enter text for PDF download button"/><br><span id="download_text_pdf_error" class="error-message" style="color: red; display: none;"></span>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Logo', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div>
							<input type="radio" id="custom_logo_option" name="logo_option" value="default" <?php checked( 'default' === $logo_option ); ?> <?php echo ( ( 'default' === $logo_option || ( '' === $logo_option ) ) ? 'checked' : '' ); ?>>
							<label for="custom_logo_option"><?php esc_html_e( 'Default', 'custom-qr-code-generator' ); ?></label>

							<input type="radio" id="upload_logo_option" name="logo_option" value="upload" <?php checked( 'upload' === $logo_option ); ?> <?php echo ( ( 'upload' === $logo_option ) ? 'checked' : '' ); ?>>
							<label for="upload_logo_option"><?php esc_html_e( 'Upload', 'custom-qr-code-generator' ); ?></label>
						</div>
						<div id="logo_fields">
							<select id="default_logo" name="default_logo" class="regular-text" <?php echo ( 'default' !== $logo_option ? 'style="display: inline;"' : '' ); ?>>
								<?php
								// Associative array of options with custom labels
								$logo_field_options = cqrc_get_logo_field_options();
								if( ! empty( $logo_field_options ) ) {
									// Loop through the array to create option elements
									foreach ( $logo_field_options as $value => $label ) {
										echo '<option value="' . esc_attr( $value ) . '" ' . selected( $default_logo_name, $value, false ) . '>' . esc_html( $label, 'custom-qr-code-generator' ) . '</option>';
									}
								}
								?>
							</select>
							<!-- <input type="file" id="upload_logo" name="upload_logo" accept=".png,.jpg,.jpeg" > -->
							<button type="button" id="upload_logo_button" class="button" <?php echo ( 'upload' === $logo_option ? 'style="display: inline;"' : '' ); ?>><?php esc_html_e( 'Select Logo', 'custom-qr-code-generator' ); ?></button>
							<?php
							if ( 'upload' === $logo_option && ! empty( $logo_option ) ) {
								echo '<img id="logo_previews" src="' . esc_url($default_logo_name) . '" alt="Logo Preview" width="30px">'; // phpcs:ignore
							}

							?>
							<?php // phpcs:disable ?>
							<img id="logo_preview" src="" alt="Logo Preview" width="30px">
							<?php // phpcs:enable ?>
							<input type="hidden" name="upload_logo_url" id="upload_logo_url" value="">
						</div>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Select QR Code Color', 'custom-qr-code-generator' ); ?></th>
					<td>
						<input type="text" id="qr_color_picker" name="qr_code_color" value="<?php echo ( !empty( $qr_code_color ) ? esc_attr( $qr_code_color ) : '#000000' ); ?>" class="wp-color-picker qr_color_picker_1" data-alpha="true" />
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Frame', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div class="select-with-frame" style="display: flex; gap: 5px;">
							<select id="default_frame" name="default_frame" class="regular-text">
								<?php
								$frame_field_options = cqrc_get_frame_field_options();
								if( ! empty( $frame_field_options ) ) {
									foreach ( $frame_field_options as $option_value => $label ) {
										echo '<option value="' . esc_attr( $option_value ) . '" ' . selected( $frame_name, $option_value, false ) . '>' . esc_html( $label ) . '</option>';
									}
								}
								?>
							</select>
							<?php // phpcs:disable ?>
							<img id="frame_preview" src="" alt="Frame Preview" width="30px">
							<?php // phpcs:enable ?>
						</div>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Frame', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div class="select-with-frame" style="display: flex; gap: 5px;">
							<select id="eye_frame_name" name="eye_frame_name" class="regular-text">
								<?php
                				// Define the frame options with custom labels
								$eye_frame_field_options = cqrc_get_eye_frame_field_options();
								if( ! empty( $eye_frame_field_options ) ) {
									foreach ( $eye_frame_field_options as $frame_option => $label ) {
										echo '<option value="' . esc_attr( $frame_option ) . '" ' . selected( $eye_frame_name, $frame_option, false ) . '>' . esc_html( $label ) . '</option>';
									}
								}
								?>
							</select>
							<?php // phpcs:disable ?>
							<img id="eye_frame_preview" src="" alt="Eye Frame Preview" width="30px">
							<?php // phpcs:enable ?>
						</div>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Frame Color', 'custom-qr-code-generator' ); ?></th>
					<td>
						<input type="text" id="qr_color_picker" name="qr_eye_frame_color" value="<?php echo ( !empty( $qr_eye_frame_color ) ? esc_attr( $qr_eye_frame_color ) : '#000000' ); ?>" class="wp-color-picker qr_color_picker_2" data-alpha="true" />
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Balls', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div class="select-with-frame" style="display: flex; gap: 5px;">
							<select id="eye_balls_name" name="eye_balls_name" class="regular-text">
								<?php
								// Associative array of options with custom labels
								$eye_balls_field_options = cqrc_get_eye_balls_field_options();
								if( ! empty( $eye_balls_field_options ) ) {
									// Loop through the array to create option elements
									foreach ( $eye_balls_field_options as $value => $label ) {
										echo '<option value="' . esc_attr( $value ) . '" ' . selected( $eye_balls_name, $value, false ) . '>' . esc_html( $label ) . '</option>';
									}
								}
								?>
							</select>
							<?php // phpcs:disable ?>
							<img id="eye_balls_preview" src="" alt="Eye Balls Preview" width="30px">
							<?php // phpcs:enable ?>
						</div>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Ball Color', 'custom-qr-code-generator' ); ?></th>
					<td>
						<input type="text" id="qr_color_picker" name="qr_eye_color" value="<?php echo ( !empty( $qr_eye_color ) ? esc_attr( $qr_eye_color ) : '#000000' ); ?>" class="wp-color-picker qr_color_picker_3" data-alpha="true" />
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Level', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div class="select-with-level" style="display: flex; gap: 5px;">
							<select id="qrcode_level" name="qrcode_level" class="regular-text">
								<?php
								// Associative array of options with custom labels
								$level_field_options = cqrc_get_level_field_options();
								if( ! empty( $level_field_options ) ) {
									// Loop through the array to create option elements
									foreach ( $level_field_options as $value => $label ) {
										echo '<option value="' . esc_attr( $value ) . '" ' . selected( $qrcode_level, $value, false ) . '>' . esc_html( $label, 'custom-qr-code-generator' ) . '</option>';
									}
								}
								?>
							</select>
						</div>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Password Protection', 'custom-qr-code-generator' ); ?></th>
					<td>
						<div class="select-with-level" style="display: flex; gap: 5px; flex-direction: column;">
							<div style="display: flex; align-items: center;">
								<input type="password" id="password" name="password" value="<?php echo ( !empty( $password ) ? esc_attr( $password ) : '' ); ?>" style="max-width:25rem" placeholder="Enter the password!">
								<span id="toggle-password" style="cursor: pointer; margin-left: 5px;" class="site-show-hide-password-ed">
									<input type="checkbox" id="site-show-hide-password"><label for="site-show-hide-password" style="cursor: pointer;"><?php esc_html_e( 'Show Password', 'custom-qr-code-generator' ); ?></label>
								</span>
							</div>
							<span id="password-error-uppercase" style="color: red;" class="show-error-message-notice">
								<i id="icon-uppercase" class="fas fa-times"></i> <?php esc_html_e( 'At least one uppercase letter.', 'custom-qr-code-generator' ); ?>
							</span>
							<span id="password-error-lowercase" style="color: red;" class="show-error-message-notice">
								<i id="icon-lowercase" class="fas fa-times"></i> <?php esc_html_e( 'At least one lowercase letter.', 'custom-qr-code-generator' ); ?>
							</span>
							<span id="password-error-digit" style="color: red;" class="show-error-message-notice">
								<i id="icon-digit" class="fas fa-times"></i> <?php esc_html_e( 'At least one digit.', 'custom-qr-code-generator' ); ?>
							</span>
							<span id="password-error-special" style="color: red;" class="show-error-message-notice">
								<i id="icon-special" class="fas fa-times"></i> <?php esc_html_e( 'At least one special character', 'custom-qr-code-generator' ); ?>
							</span>
							<span><strong><?php esc_html_e( 'You can password protect a QR code to secure the information (Max 10 characters!).', 'custom-qr-code-generator' ); ?></strong></span>
							
						</div>
					</td>
				</tr>
			</table>
			<button type="button" id="toggle-settings" class="button button-secondary">
				<?php esc_html_e( 'Show Additional Settings', 'custom-qr-code-generator' ); ?>
			</button>
		</div>
		<div class="col-md-3" style="padding-left: 20px;width: 30%;display: block;position: sticky !important;top: 50px !important;height: 100%;">
			<h2><?php esc_html_e( 'QR Code Previous', 'custom-qr-code-generator' ); ?></h2>
			<?php if ( !empty( $qrcode ) && ! empty( $qrcode ) ) { // phpcs:disable ?>
				<img src="<?php echo esc_url( $qrcode ); ?>" width="50%" id="qrcode_default">
			<?php } ?>
			<img id="qrcode_image"  src="<?php echo esc_url( !empty( $qrcode ) ? $qrcode : $default_image ); ?>" width="50%" style="display: <?php echo !empty( $qrcode ) ? 'none' : 'block'; ?>">
			<?php // phpcs:enable ?>
		</div>
	</div>
	<div class="form-buttons">
		<?php 
		$button_text = !empty($ids) ? __('Update QR Code', 'custom-qr-code-generator') : __('Generate QR Code', 'custom-qr-code-generator');
		submit_button($button_text); 
		?>
	</div>
</form>
