<?php
/**
 * About .
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

<div class="wrap">
	<h1><?php esc_html_e( 'About QR Code Generator Plugin', 'custom-qrcode-generator' ); ?></h1>

	<div class="plugin-descriptions">
		<p><?php esc_html_e( 'Welcome to the QR Code Generator Plugin! This powerful tool allows you to easily generate and manage QR codes on your WordPress site with just a few clicks.', 'custom-qrcode-generator' ); ?></p>
	</div>

	<h2><?php esc_html_e( 'Using the Shortcode', 'custom-qrcode-generator' ); ?></h2>
	<div class="shortcode-info">
		<p><?php esc_html_e( 'You can use the following shortcode to display a specific QR code on any page or post on your website. Simply insert this shortcode into your content:', 'custom-qrcode-generator' ); ?></p>
		<div class="shortcode-example">
			<pre id="shortcode-code"><code>[cqrc_gen_qrcode_view id="32"]</code></pre><span id="copy-message" style="display: none; color: green; margin-left: 10px;"><?php esc_html_e( 'Code copied!!!', 'custom-qrcode-generator' ); ?></span>
			<span id="copy-code-icon" class="dashicons dashicons-admin-page" style="cursor: pointer; font-size: 20px; margin-left: 10px;" title="<?php esc_attr_e( 'Copy to clipboard', 'custom-qrcode-generator' ); ?>"></span>
		</div>
		<div>
			<p class="shortcode-notes-desctiption"><?php esc_html_e( 'Description: ', 'custom-qrcode-generator' ); ?></p>
			<ul>
				<li>
					<?php esc_html_e( 'Replace "32" with the ID of the QR code you want to display.', 'custom-qrcode-generator' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'This shortcode will render the QR code associated with the specified ID directly in the content.', 'custom-qrcode-generator' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Additionally, this shortcode enables users to download the QR code by clicking on it.', 'custom-qrcode-generator' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'The functionality to track the number of users who have scanned a certain QR code has been introduced, and the main page for the code allows you to view the total number of scans.', 'custom-qrcode-generator' ); ?>
				</li>
			</ul>
		</div>
	</div>

	<h2><?php esc_html_e( 'About Us', 'custom-qrcode-generator' ); ?></h2>
	<div class="about-us-company-details">
		<p><?php esc_html_e( 'We are a service-based company specializing in WordPress website development. Based in India, our mission is to provide high-quality, customized WordPress solutions to help businesses succeed online.', 'custom-qrcode-generator' ); ?></p>
		<p><?php esc_html_e( 'For more information about our services and how we can assist you, please visit our website:', 'custom-qrcode-generator' ); ?> <a href="https://www.worldwebtechnology.com/" target="_blank"><strong><?php esc_html_e( 'World Web Technology', 'custom-qrcode-generator' ); ?></strong></a></p>
	</div>
</div>