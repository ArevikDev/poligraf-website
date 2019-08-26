=== QR Code for WooCommerce ===
Contributors: wcbeginner,subratamal,bappa1995
Tags: QR Codes, Quick Response Code, woocommerce qr code
Donate link: https://www.paypal.me/SubrataMal941
Requires PHP: 5.6
Requires at least: 4.4
Tested up to: 5.2
Stable tag: 1.2.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Generate QR code for WooCommerce Products

== Description ==

In a world of smartphones, the term QR code (abbreviated from Quick Response Code) and it's uses are no more alien to us. Each QR code is unique and can be associated with any object/entity. 

The plugin WooCommerce QR code generator does exactly what it's name say. It generates an unique QR code every time you create a product in your WooCommerce store and associates this code with the product's URL. Next time you want to open your product  in mobile, no need to look to and fro between your desktop and mobile. Just scan the code and your phone's browser will automatically open that product URL. 

The plugin even let's you choose the QR code size as well as the code's frame size. Happy scanning!

> Join the cashless Revolution with [TeraWallet](https://wordpress.org/plugins/woo-wallet/) plugin, the leading wallet plugin with partial payment, refunds, cashbacks and what not!

Use of wc_qr_code Shortcode:
`[wc_qr_code id='{put product_id}' title='{put QR Code heading}'][/wc_qr_code]`

== Installation ==

1. Download and install WooCommerce QR Codes plugin using the built-in WordPress plugin installer.
If you download WooCommerce QR Codes plugin manually, make sure it is uploaded to `/wp-content/plugins/` and activate the plugin through the `Plugins` menu in WordPress. Or follow the steps below:
Plugins > Add new > Upload plugin > Upload woocommerce-qr-codes.zip > Install Now > Activate Plugin.

NOTE: Required PHP GD library for this plugin to work properly

== Frequently Asked Questions ==

= Does this plugin work with newest WP version and also older versions? =
Ans. Yes, this plugin works fine with WordPress 5.2! It is also compatible for older WordPress versions upto 4.2.
= Up to which version of WooCommerce this plugin compatible with? =
Ans. This plugin is compatible with the latest version of WooCommerce.

== Screenshots ==

1. Admin section
2. Frontend section
3. Setting section

== Changelog ==

= 1.2.0 - 07-06-2019 =
* Fix: Regenerate all QR code from settings page.
* Fix: Plugin installation problem.

= 1.1.0 - 18-03-2018 =
* Modify - QR Code menu id.
* Added - filter `wc_qr_code_account_title`.

= 1.0.7 - 03-01-2018 =
* Added: Regenerate all QR code functionality.
* Added: Blank html file in qr code upload directory for security purpose.
* Added: Plugin settings link in WP plugin list page. 
* Updated: Language file.

= 1.0.6 - 02-01-2018 =
* Added: WP Multisite support.

= 1.0.5 = 
* Added: `wc_qr_code` Shortcode.
* Updated: Language file

= 1.0.4 =
* Added: Wordpress 4.8 support
* Added: Filter <strong>wcqrc_product_permalink</strong> to alter qr code link
* Fixed: Language support

= 1.0.3 =
* Added: Function to fetch product wise QR code
* Fixed: Language support
* Updated: Language file

= 1.0.2 =
* Added: Setting panel for setup qr code size and frame size
* Added: Regenerate qr code feature
* Added: WooCommerce 3.0 support
* Updated: language file

= 1.0.1 =
* Added: Product tab for display Product QR Code 

= 1.0.0 =
* Initial Release 


== Upgrade Notice ==

= 1.0.7 =
* Added: Regenerate all QR code functionality.
* Added: Blank html file in qr code upload directory for security purpose.
* Added: Plugin settings link in WP plugin list page. 
* Updated: Language file.