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

$qr_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;

if ( $qr_id > 0 ) {

	// Check nonce
	if (!isset($_REQUEST['_qr_code_nonce_action']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['_qr_code_nonce_action'])), 'qr_code_nonce_action')) {
		wp_die(esc_html__('Nonce verification failed. Please refresh and try again.', 'custom-qrcode-generator'));
	}

	global $wpdb;
	$table_name = $wpdb->prefix . 'qrcode_generator';
	$qr_data    = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}qrcode_generator WHERE ID = %d", $qr_id ), ARRAY_A ); // phpcs:ignore

	if ( $qr_data ) {
		$ids                = $qr_data['id'];
		$name               = $qr_data['name'];
		$qrcode_description = $qr_data['description'];
		$qrcode             = $qr_data['qr_code'];
		$url                = $qr_data['url'];
		$default_logo_name  = $qr_data['default_logo_name'];
		$template_name      = $qr_data['template_name'];
		$frame_name         = $qr_data['frame_name'];
		$eye_frame_name     = $qr_data['eye_frame_name'];
		$eye_balls_name     = $qr_data['eye_balls_name'];
		$qr_status          = $qr_data['status'];
		$password           = $qr_data['password'];
		$logo_option        = $qr_data['upload_logo'];
		$qr_eye_color       = $qr_data['qr_eye_color'];
		$qr_eye_frame_color = $qr_data['qr_eye_frame_color'];
		$qr_code_color      = $qr_data['qr_code_color'];
		$qrcode_level       = $qr_data['qrcode_level'];
	} else {
		echo 'QR Code not found.';
		exit;
	}
} else {
	$ids                = '';
	$name               = '';
	$qrcode_description 		= '';
	$url                = '';
	$logo_option        = '';
	$default_logo_name  = '';
	$template_name      = '';
	$frame_name         = '';
	$qr_status          = '';
	$password     		= '';
	$logo_option        = '';
	$qr_eye_color       = '#000000';
	$qr_code_color      = '#000000';
	$qr_eye_frame_color = '#000000';
	$qrcode_level       = '';
	$eye_frame_name     = '';
	$eye_balls_name     = '';
}

$default_image = CQRCGEN_ADMIN_URL. '/assets/qrcode/dashicon/google-intro.png';
?>
<h1>
	<?php
	esc_html_e( 'QRCode Generator', 'custom-qrcode-generator' );
	?>
</h1>
<style type="text/css">#toggle-password i { transition: color 0.3s; } #toggle-password:hover i {color: #0073aa; } </style>
<form method="post" action="" enctype="multipart/form-data" id="wwt-qrcode-generate-form">
	<div id="qrcode-loader" style="display: none;"></div>
	<?php wp_nonce_field( 'qr_code_form_data', 'qr_code_form_data_nonce' ); ?>
	<input type="hidden" value="<?php echo ( $ids ? esc_attr( $ids ) : '' ); ?>" name="qrid">
	<div class="row" style="display:flex;">
		<div class="col-md-9" style="width: 70%; display: block">
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'URL', 'custom-qrcode-generator' ); ?><span class="required-lables">*</span></th>
					<td><input type="url" name="qrcode_url" id="qrcode_url" value="<?php echo ( $url ? esc_url( $url ) : '' ); ?>" class="regular-text" placeholder="https://www.google.com/" required/><br><span id="url_error" class="error-message" style="color: red; display: none;"><?php __('Maximum length is 75 characters.','custom-qrcode-generator'); ?></span></td>
				</tr>
				
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Name', 'custom-qrcode-generator' ); ?><span class="required-lables">*</span></th>
					<td><input type="text" name="qrcode_name" id="qrcode_name" value="<?php echo ( $name ? esc_attr( $name ) : '' ); ?>" class="regular-text" placeholder="Website Introduction" required/><br><span id="name_error" class="error-message" style="color: red; display: none;"><?php __('Maximum length is 30 characters & Only Allowed Character.','custom-qrcode-generator'); ?></span></td>
				</tr>
				
				
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Description', 'custom-qrcode-generator' ); ?><span class="required-lables">*</span></th>
					<td>
						<textarea name="qrcode_description" id="qrcode_description" rows="5" cols="3" class="regular-text" placeholder="Enter description here..." required><?php echo ( esc_textarea($qrcode_description) ? esc_textarea($qrcode_description) : '' ); ?></textarea><br>
						<span id="description_error" class="error-message" style="color: red; display: none;"><?php __('Maximum word count is 100.','custom-qrcode-generator'); ?></span>
					</td>
				</tr>
				
				<tr valign="top" class="additional-settingss">
					<th scope="row"><?php esc_html_e( 'Default Templates', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div class="select-with-default-template" style="display: flex; gap: 5px;">
							<select id="template_name" name="template_name" class="regular-text">
								<?php
								// Define custom labels for each template option
								$template_options = array(
									'default' => __('Default', 'custom-qrcode-generator'),
									'facebook' => __('Facebook', 'custom-qrcode-generator'),
									'youtube-circle' => __('YouTube', 'custom-qrcode-generator'),
									'twitter-circle' => __('Twitter', 'custom-qrcode-generator'),
									'instagram-circle' => __('Instagram', 'custom-qrcode-generator'),
									'whatsapp-circle' => __('WhatsApp', 'custom-qrcode-generator'),
									'gmail' => __('Gmail', 'custom-qrcode-generator'),
									'linkedin-circle' => __('LinkedIn', 'custom-qrcode-generator'),
								);

								// Loop through the options array
								foreach ( $template_options as $option_value => $label ) {
									echo '<option value="' . esc_attr( $option_value ) . '" ' . selected( $template_name, $option_value, false ) . '>' . esc_html( $label ) . '</option>';
								}
								?>
							</select>
							<i class="fas fa-chevron-down"></i> 
							<img id="template_preview" src="" alt="Template Preview" width="30px">
						</div>
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Logo', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div>
							<input type="radio" id="custom_logo_option" name="logo_option" value="default" <?php checked( 'default' === $logo_option ); ?> <?php echo ( ( 'default' === $logo_option || ( '' === $logo_option ) ) ? 'checked' : '' ); ?>>
							<label for="custom_logo_option"><?php esc_html_e( 'Default', 'custom-qrcode-generator' ); ?></label>

							<input type="radio" id="upload_logo_option" name="logo_option" value="upload" <?php checked( 'upload' === $logo_option ); ?> <?php echo ( ( 'upload' === $logo_option ) ? 'checked' : '' ); ?>>
							<label for="upload_logo_option"><?php esc_html_e( 'Upload', 'custom-qrcode-generator' ); ?></label>
						</div>

						<div id="logo_fields">
							<select id="default_logo" name="default_logo" class="regular-text" <?php echo ( 'default' !== $logo_option ? 'style="display: inline;"' : '' ); ?>>
								<option value="default" <?php selected( basename( $default_logo_name ), 'default' ); ?>><?php esc_html_e( 'Default', 'custom-qrcode-generator' ); ?></option>
								<option value="instagram-circle" <?php selected( basename( $default_logo_name ), 'instagram-circle.png' ); ?>><?php esc_html_e( 'Instagram', 'custom-qrcode-generator' ); ?></option>
								<option value="facebook" <?php selected( basename( $default_logo_name ), 'facebook.png' ); ?>><?php esc_html_e( 'Facebook', 'custom-qrcode-generator' ); ?></option>
								<option value="youtube-circle" <?php selected( basename( $default_logo_name ), 'youtube-circle.png' ); ?>><?php esc_html_e( 'YouTube', 'custom-qrcode-generator' ); ?></option>
								<option value="whatsapp-circle" <?php selected( basename( $default_logo_name ), 'whatsapp-circle.png' ); ?>><?php esc_html_e( 'WhatsApp', 'custom-qrcode-generator' ); ?></option>
								<option value="linkedin-circle" <?php selected( basename( $default_logo_name ), 'linkedin-circle.png' ); ?>><?php esc_html_e( 'LinkedIn', 'custom-qrcode-generator' ); ?></option>
								<option value="twitter-circle" <?php selected( basename( $default_logo_name ), 'twitter-circle.png' ); ?>><?php esc_html_e( 'Twitter', 'custom-qrcode-generator' ); ?></option>
								<option value="gmail" <?php selected( basename( $default_logo_name ), 'gmail.png' ); ?>><?php esc_html_e( 'Gmail', 'custom-qrcode-generator' ); ?></option>
								<option value="google-play" <?php selected( basename( $default_logo_name ), 'google-play.png' ); ?>><?php esc_html_e( 'Google Play', 'custom-qrcode-generator' ); ?></option>
								<option value="googleplus-circle" <?php selected( basename( $default_logo_name ), 'googleplus-circle.png' ); ?>><?php esc_html_e( 'Google Plus', 'custom-qrcode-generator' ); ?></option>
								<option value="xing-circle" <?php selected( basename( $default_logo_name ), 'xing-circle.png' ); ?>><?php esc_html_e( 'Xing', 'custom-qrcode-generator' ); ?></option>
								<option value="google-calendar" <?php selected( basename( $default_logo_name ), 'google-calendar.png' ); ?>><?php esc_html_e( 'Google Calendar', 'custom-qrcode-generator' ); ?></option>
								<option value="google-forms" <?php selected( basename( $default_logo_name ), 'google-forms.png' ); ?>><?php esc_html_e( 'Google Forms', 'custom-qrcode-generator' ); ?></option>
								<option value="google-maps" <?php selected( basename( $default_logo_name ), 'google-maps.png' ); ?>><?php esc_html_e( 'Google Maps', 'custom-qrcode-generator' ); ?></option>
								<option value="google-meet" <?php selected( basename( $default_logo_name ), 'google-meet.png' ); ?>><?php esc_html_e( 'Google Meet', 'custom-qrcode-generator' ); ?></option>
								<option value="google-sheets" <?php selected( basename( $default_logo_name ), 'google-sheets.png' ); ?>><?php esc_html_e( 'Google Sheets', 'custom-qrcode-generator' ); ?></option>
								<option value="hangouts-meet" <?php selected( basename( $default_logo_name ), 'hangouts-meet.png' ); ?>><?php esc_html_e( 'Hangouts Meet', 'custom-qrcode-generator' ); ?></option>
								<option value="spotify" <?php selected( basename( $default_logo_name ), 'spotify.png' ); ?>><?php esc_html_e( 'Spotify', 'custom-qrcode-generator' ); ?></option>
								<option value="telegram" <?php selected( basename( $default_logo_name ), 'telegram.png' ); ?>><?php esc_html_e( 'Telegram', 'custom-qrcode-generator' ); ?></option>
							</select>
							<!-- <input type="file" id="upload_logo" name="upload_logo" accept=".png,.jpg,.jpeg" > -->
							<button type="button" id="upload_logo_button" class="button" <?php echo ( 'upload' === $logo_option ? 'style="display: inline;"' : '' ); ?>><?php esc_html_e( 'Select Logo', 'custom-qrcode-generator' ); ?></button>
							<?php
							if ( 'upload' === $logo_option && ! empty( $logo_option ) ) {
								echo '<img id="logo_previews" src="' . esc_url($default_logo_name) . '" alt="Logo Preview" width="30px">';
							}

							?>
							<img id="logo_preview" src="" alt="Logo Preview" width="30px">
							<input type="hidden" name="upload_logo_url" id="upload_logo_url" value="">
						</div>
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Select QR Code Color', 'custom-qrcode-generator' ); ?></th>
					<td>
						<input type="text" id="qr_color_picker" name="qr_code_color" value="<?php echo ( isset( $qr_code_color ) ? esc_attr( $qr_code_color ) : '#000000' ); ?>" class="wp-color-picker qr_color_picker_1" data-alpha="true" />
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Frame', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div class="select-with-frame" style="display: flex; gap: 5px;">
							<select id="default_frame" name="default_frame" class="regular-text">
								<?php
								$frame_options = array(
									'default' => __('Default Frame', 'custom-qrcode-generator'),
									'balloon-bottom' => __('Balloon Bottom Scan', 'custom-qrcode-generator'),
									'balloon-bottom-1' => __('Balloon Bottom Review', 'custom-qrcode-generator'),
									'balloon-top' => __('Balloon Top Scan', 'custom-qrcode-generator'),
									'balloon-top-2' => __('Balloon Top Review', 'custom-qrcode-generator'),
									'banner-bottom' => __('Banner Bottom Scan', 'custom-qrcode-generator'),
									'banner-bottom-3' => __('Banner Bottom Review', 'custom-qrcode-generator'),
									'banner-top' => __('Banner Top Scan', 'custom-qrcode-generator'),
									'banner-top-4' => __('Banner Top Review', 'custom-qrcode-generator'),
									'box-bottom' => __('Box Bottom Scan', 'custom-qrcode-generator'),
									'box-bottom-5' => __('Box Bottom Review', 'custom-qrcode-generator'),
									'box-top' => __('Box Top Scan', 'custom-qrcode-generator'),
									'box-top-6' => __('Box Top Review', 'custom-qrcode-generator'),
									'focus-8-lite' => __('Focus Scan', 'custom-qrcode-generator'),
									'focus-lite' => __('Focus Review', 'custom-qrcode-generator'),
								);

								foreach ( $frame_options as $option_value => $label ) {
									echo '<option value="' . esc_attr( $option_value ) . '" ' . selected( $frame_name, $option_value, false ) . '>' . esc_html( $label ) . '</option>';
								}
								?>
							</select>
							<i class="fas fa-chevron-down"></i> 
							<img id="frame_preview" src="" alt="Frame Preview" width="30px">
						</div>
					</td>
				</tr>


				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Frame', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div class="select-with-frame" style="display: flex; gap: 5px;">
							<select id="eye_frame_name" name="eye_frame_name" class="regular-text">
								<?php
                				// Define the frame options with custom labels
								$eye_frame_options = array(
									'default'    => __('Default Frame', 'custom-qrcode-generator'),
									'frame0'     => __('Square', 'custom-qrcode-generator'),
									'frame1'     => __('Messanger', 'custom-qrcode-generator'),
									'frame2'     => __('Glow', 'custom-qrcode-generator'),
									'frame3'     => __('Glare', 'custom-qrcode-generator'),
									'frame4'     => __('Square Dots', 'custom-qrcode-generator'),
									'frame5'     => __('Qutes', 'custom-qrcode-generator'),
									'frame6'     => __('Square Cut', 'custom-qrcode-generator'),
									'frame7'     => __('Square Scrached', 'custom-qrcode-generator'),
									'frame8'     => __('Square lined', 'custom-qrcode-generator'),
									'frame9'     => __('Square dashed', 'custom-qrcode-generator'),
									'frame10'    => __('Square Bold', 'custom-qrcode-generator'),
									'frame11'    => __('Square Bold Dots', 'custom-qrcode-generator'),
									'frame12'    => __('Circle', 'custom-qrcode-generator'),
									'frame13'    => __('Rectangle', 'custom-qrcode-generator'),
									'frame14'    => __('Outline', 'custom-qrcode-generator'),
								);

								foreach ( $eye_frame_options as $frame_option => $label ) {
									echo '<option value="' . esc_attr( $frame_option ) . '" ' . selected( $eye_frame_name, $frame_option, false ) . '>' . esc_html( $label ) . '</option>';
								}
								?>
							</select>
							<i class="fas fa-chevron-down"></i> 
							<img id="eye_frame_preview" src="" alt="Eye Frame Preview" width="30px">
						</div>
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Frame Color', 'custom-qrcode-generator' ); ?></th>
					<td>
						<input type="text" id="qr_color_picker" name="qr_eye_frame_color" value="<?php echo ( isset( $qr_eye_frame_color ) ? esc_attr( $qr_eye_frame_color ) : '#000000' ); ?>" class="wp-color-picker qr_color_picker_2" data-alpha="true" />
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Balls', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div class="select-with-frame" style="display: flex; gap: 5px;">
							<select id="eye_balls_name" name="eye_balls_name" class="regular-text">
								<?php
								// Associative array of options with custom labels
								$eye_balls_options = array(
									'default'   => __('Default', 'custom-qrcode-generator'),
									'ball0'     => __('Square', 'custom-qrcode-generator'),
									'ball1'     => __('Messanger', 'custom-qrcode-generator'),
									'ball2'     => __('Glow', 'custom-qrcode-generator'),
									'ball3'     => __('Glare', 'custom-qrcode-generator'),
									'ball4'     => __('Hexagon', 'custom-qrcode-generator'),
									'ball5'     => __('Dots', 'custom-qrcode-generator'),
									'ball6'     => __('Square Cut', 'custom-qrcode-generator'),
									'ball7'     => __('Square Lining', 'custom-qrcode-generator'),
									'ball8'     => __('Square Scrached', 'custom-qrcode-generator'),
									'ball9'     => __('Octa', 'custom-qrcode-generator'),
									'ball10'    => __('Octa Dots', 'custom-qrcode-generator'),
									'ball11'    => __('G-Messanger', 'custom-qrcode-generator'),
									'ball12'    => __('Horizontal Menu', 'custom-qrcode-generator'),
									'ball13'    => __('Verticle Menu', 'custom-qrcode-generator'),
									'ball14'    => __('Dot', 'custom-qrcode-generator'),
									'ball15'    => __('Rectangle Square', 'custom-qrcode-generator'),
									'ball16'    => __('Outline', 'custom-qrcode-generator'),
									'ball17'    => __('Diamond', 'custom-qrcode-generator'),
									'ball18'    => __('Star', 'custom-qrcode-generator'),
									'ball19'    => __('Verified', 'custom-qrcode-generator'),
									'ball20'    => __('Octagon', 'custom-qrcode-generator'),
									'ball21'    => __('Triangle', 'custom-qrcode-generator')
								);

								// Loop through the array to create option elements
								foreach ( $eye_balls_options as $value => $label ) {
									echo '<option value="' . esc_attr( $value ) . '" ' . selected( $eye_balls_name, $value, false ) . '>' . esc_html( $label ) . '</option>';
								}
								?>
							</select>
							<i class="fas fa-chevron-down"></i> 
							<img id="eye_balls_preview" src="" alt="Eye Balls Preview" width="30px">
						</div>
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Eye Ball Color', 'custom-qrcode-generator' ); ?></th>
					<td>
						<input type="text" id="qr_color_picker" name="qr_eye_color" value="<?php echo ( isset( $qr_eye_color ) ? esc_attr( $qr_eye_color ) : '#000000' ); ?>" class="wp-color-picker qr_color_picker_3" data-alpha="true" />
					</td>
				</tr>

				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Level', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div class="select-with-level" style="display: flex; gap: 5px;">
							<select id="qrcode_level" name="qrcode_level" class="regular-text">
								<option value="QR_ECLEVEL_H" <?php selected( $qrcode_level, 'QR_ECLEVEL_H' ); ?>><?php esc_html_e( 'Level H', 'custom-qrcode-generator' ); ?></option>	
								<option value="QR_ECLEVEL_Q" <?php selected( $qrcode_level, 'QR_ECLEVEL_Q' ); ?>><?php esc_html_e( 'Level Q', 'custom-qrcode-generator' ); ?></option>
								<option value="QR_ECLEVEL_M" <?php selected( $qrcode_level, 'QR_ECLEVEL_M' ); ?>><?php esc_html_e( 'Level M', 'custom-qrcode-generator' ); ?></option>
							</select>
						</div>
					</td>
				</tr>
				<tr valign="top" class="additional-settings">
					<th scope="row"><?php esc_html_e( 'Password Protection', 'custom-qrcode-generator' ); ?></th>
					<td>
						<div class="select-with-level" style="display: flex; gap: 5px; flex-direction: column;">
							<div style="display: flex; align-items: center;">
								<input type="password" id="password" name="password" value="<?php echo ( isset( $password ) ? esc_attr( $password ) : '' ); ?>" style="max-width:25rem" placeholder="Enter the password!">
								<span id="toggle-password" style="cursor: pointer; margin-left: 5px;">
									<input type="checkbox" id="site-show-hide-password">Show Password
								</span>
							</div>
							<span id="password-error" style="color: red; display: none;"></span>
							<span><?php esc_html_e( 'You can password protect a QR code to secure the information.', 'custom-qrcode-generator' ); ?></span>
						</div>
					</td>
				</tr>
			</table>
			<button type="button" id="toggle-settings" class="button button-secondary">
				<?php esc_html_e( 'Show Additional Settings', 'custom-qrcode-generator' ); ?>
			</button>
		</div>
		<div class="col-md-3" style="padding-left: 20px; width: 30%; display: block;">
			<h2><?php esc_html_e( 'QR Code Previous', 'custom-qrcode-generator' ); ?></h2>
			<?php if ( isset( $qrcode ) && ! empty( $qrcode ) ) { ?>
				<img src="<?php echo esc_url( $qrcode ); ?>" width="50%" id="qrcode_default">
			<?php } ?>
			<img id="qrcode_image"  src="<?php echo esc_url( isset( $qrcode ) ? $qrcode : $default_image ); ?>" width="50%" style="display: <?php echo isset( $qrcode ) ? 'none' : 'block'; ?>">
		</div>
	</div>
	<div class="form-buttons">
		<?php 
		$button_text = !empty($ids) ? __('Update QR Code', 'custom-qrcode-generator') : __('Generate QR Code', 'custom-qrcode-generator');
		submit_button($button_text); 
		if ( empty( $qr_id ) ) {
			?>
			<p  class="reset-button"><button type="reset" value="Reset Form" class="button button-primary"><?php esc_html_e( 'Reset Form', 'custom-qrcode-generator'  ); ?></button></p>
			<?php
		} ?>
	</div>
</form>
<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		var toggleButton = document.getElementById('toggle-settings');
		var settingsElements = document.querySelectorAll('.additional-settings');
		var isVisible = false;

		toggleButton.addEventListener('click', function() {
			isVisible = !isVisible;
			settingsElements.forEach(function(el) {
				el.style.display = isVisible ? 'table-row' : 'none';
			});
			toggleButton.textContent = isVisible ? '<?php esc_html_e( 'Hide Additional Settings', 'custom-qrcode-generator' ); ?>' : '<?php esc_html_e( 'Show Additional Settings', 'custom-qrcode-generator' ); ?>';
		});
	});
	
	jQuery(document).ready(function($) {
		var maxWords = 100;
		var subbtn = $('.form-buttons p.submit input#submit');

		function countWords(str) {
			return str.trim().split(/\s+/).length;
		}

		$('#qrcode_description').on('input', function() {
			var textareaValue = $(this).val();
			var wordCount = countWords(textareaValue);

			if (wordCount > maxWords) {
				$('#description_error').show();
				subbtn.prop('disabled', true);
			} else {
				$('#description_error').hide();
				subbtn.prop('disabled', false);
			}
		});
	});
	jQuery(document).ready(function($) {
		$('#site-show-hide-password').on('click', function() {
			var passwordInput = $('#password');
			var passwordFieldType = passwordInput.attr('type');

			if (passwordFieldType === 'password') {
				passwordInput.attr('type', 'text');
				$(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
			} else {
				passwordInput.attr('type', 'password');
				$(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
			}
		});
		function validatePassword(password) {
			var errors = [];
			if (password.length > 10) {
				errors.push("Password must be at most 10 characters.");
			}
			if (!/[A-Z]/.test(password)) {
				errors.push("Password must contain at least one uppercase letter.");
			}
			if (!/[a-z]/.test(password)) {
				errors.push("Password must contain at least one lowercase letter.");
			}
			if (!/[0-9]/.test(password)) {
				errors.push("Password must contain at least one digit.");
			}
			if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
				errors.push("Password must contain at least one special character.");
			}
			return errors;
		}

		$('#password').on('input', function() {
			var password = $(this).val();
			var errors = validatePassword(password);
			
			if (errors.length > 0) {
				$('#password-error').html(errors.join('<br>')).show();
			} else {
				$('#password-error').hide();
			}
		});
	});
</script>