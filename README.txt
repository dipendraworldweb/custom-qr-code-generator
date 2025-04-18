=== Custom QR Code Generator ===
Contributors:      worldweb
Plugin Name:       Custom QR Code Generator
Plugin URI:        https://loancalc.worldwebtechnology.com/custom-qr-code-generator-document/
Tags:              QR code, QR Code Wordpress Plugin, QR Code Generator, QR Code shortcodes, QR code in page/post.
Author:            World Web Technology
Author URI:        https://www.worldwebtechnology.com/
Requires at least: 5.6
Requires PHP:      7.4
Tested up to:      6.8
Stable tag:        1.0.2
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Version:           1.0.2

Easily generate customizable QR codes for websites, products, and events with this user-friendly WordPress plugin.

== Description ==

[Demo](https://qrcode.worldwebtechnology.com/) | [Docs](https://qrcode.worldwebtechnology.com/custom-qr-code-generator-document/)  |  [Support](mailto:help.worldweb@gmail.com)  | [Website](https://www.worldwebtechnology.com/)

The "Custom QR Code Generator" plugin for WordPress is a powerful tool that allows users to easily create customizable QR codes for various purposes, including sharing links, promoting products, and providing essential information about events or social media profiles.

This plugin uses the Chillerlan PHP QR Code library.

**Main Features**
- Simple to use and easy to install.
- Clean and modern design.
- Highly customizable to fit your needs.
- Password protection for QR codes.
- Import and export QR codes functionality.
- Log user details from scanned QR codes.

**Best Used For**
- Sharing Website Links.
- Product Information.
- Event Details.
- Payment Information.

== Installation Instructions ==

1. Unzip the downloaded zip file.
2. Upload the included folder to the `/wp-content/plugins` directory of your WordPress installation.
3. Activate the plugin via the WordPress Plugins page.

== Usage ==

To display a specific QR code on any page or post on your website, use the following shortcode:

`[cqrc_gen_qrcode_view id="32"]`

Replace "32" with the ID of the QR code you want to display.

== Third party library ==

1. PHP QR Code library. 
   - The plugin uses the PHP QR Code generator library. [GIT](https://github.com/chillerlan/php-qrcode).
   
== Frequently Asked Questions ==

= 1. How do I create a QR code? =
After installing and activating the plugin, navigate to the QR Code Generator menu in the admin dashboard, fill in the required fields, and click "Generate".

= 2. What file formats are supported for QR code output? =
The plugin currently supports JPG, PNG, PDF format for QR code output.

= 3. How can I integrate the QR code into my website? =
Use the provided shortcode code to insert the QR code into your web pages.

= 4. Is there a limit to the amount of data that can be encoded in a QR code? =
QR codes can store a substantial amount of data, but larger amounts of data may lead to more complex QR codes that are harder to scan. The plugin typically handles standard data sizes, but check the documentation for any specific limits.

= 5. How secure is the data encoded in a QR code? =
The security of the data encoded in a QR code depends on how the QR code is used and shared. The QR code itself does not encrypt the data, so if sensitive information is encoded, consider using additional encryption methods or secure channels.

= 6. Can I use the plugin to generate QR codes in bulk? =
Yes, the plugin supports generating QR codes in bulk.

= 7. How can I customize the appearance of the QR code beyond color? =
The plugin may allow customization of color and size, but more advanced styling options such as adding logos or changing shapes may require additional plugins or custom development.

= 8. Is there a way to track QR code scans? =
Yes, the plugin provides basic tracking capabilities for QR code scans. Specifically, you can track the number of times each QR code is scanned.

= 9. How do I update the plugin to the latest version? =
Check the plugin’s official website or repository for updates. Follow the update instructions provided, which usually involve downloading the latest version and replacing the existing files.

= 10. How can I get help if I encounter issues with the plugin? =
For help, refer to the plugin’s support page, consult the user forums, or contact the support team via email or through the platform’s support channels.

= 11. Is the plugin compatible with the latest version of WordPress? =
Yes, the plugin is now fully compatible with WordPress version 6.8. We have made necessary adjustments to ensure seamless integration and optimal performance with this version.

= 12. What is the embedded QR code functionality introduced in version 1.0.2? =
The embedded QR code functionality allows users to generate and display QR codes directly within the plugin interface. This feature simplifies the process of sharing information and enhances user engagement by providing a quick and easy way to access content.

== Screenshots ==

1. Qr Code Generation Form
2. Qr Code Additional Settings
3. Generated QR Code Backend Listing
4. All Scanned Records Backend Listing
5. Export QR Codes Feature with Fields Selection
6. Import QR Code Feature with Sample CSV File
7. Basic Introduction of the plugin
8. Frontend View of the Shortcode with Download QR Code Buttons

== External Services ==

This plugin utilizes the following external services for analytics and user information:

1. IPinfo
   - To retrieve user location data based on their IP address. This includes details such as city, region, country, and organization.
   - For more information, please refer to their [Privacy Policy](https://ipinfo.io/privacy-policy) and [Terms of Service](https://ipinfo.io/terms-of-service).

2. User Agent Detection
   - To determine the type of device (Mobile or Desktop) that the user is using.

== Changelog ==

= 1.0.2 (Apr 15, 2025) =
* Implemented embedded QR code functionality for enhanced user experience.
* Also, Compatibility with WordPress version 6.8, ensuring seamless integration and functionality.

= 1.0.1 (Mar 24, 2025) =
* Resolved QR code scan password form visible issue.

= 1.0.0 (Feb 19, 2025) =
* Initial release.