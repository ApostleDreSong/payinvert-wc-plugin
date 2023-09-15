=== PayInvert Payment Gateway ===
Contributors: Damilare ADEMESO
Tags: payment gateway, WooCommerce, payment processing
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

The PayInvert Payment Gateway plugin for WooCommerce allows you to accept payments in Naira using PayInvert. Merchants can choose between the iFrame and redirect methods for accepting payments.

**Features:**

- Accept Naira payments through PayInvert.
- Secure and reliable payment processing.
- Choose between iFrame and redirect payment methods.

**Installation**

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install it through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to WooCommerce > Settings > Payments and configure the PayInvert payment gateway with your API credentials and webhook settings.

**Configuration**

To configure the plugin, follow these steps:

1. **API Keys:**
   - Log in to your PayInvert dashboard.
   - Obtain your Public API Key and Encryption Key.
   - In your WordPress admin panel, go to WooCommerce > Settings > Payments > PayInvert.
   - Enter your Public API Key and Encryption Key in the respective fields.

2. **Webhook Configuration:**
   - In your PayInvert dashboard, copy the webhook URL provided.
   - In your WordPress admin panel, go to WooCommerce > Settings > Payments > PayInvert.
   - Paste the webhook URL into the Webhook URL field.

3. **Authorization Header:**
   - In your PayInvert dashboard, find the Authorization Header Name and Authorization Header Value.
   - In your WordPress admin panel, go to WooCommerce > Settings > Payments > PayInvert.
   - Enter the Authorization Header Name and Authorization Header Value in the respective fields.

4. **Payment Methods:**
   - Choose your preferred payment method: iFrame or redirect.
   - Save your settings.

**Note**: This plugin is designed for Naira transactions only.

== Frequently Asked Questions ==

= Is this plugin compatible with the latest version of WooCommerce? =

Yes, this plugin is compatible with WooCommerce version 5.0 and higher.

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release.

== Screenshots ==

1. [Screenshot 1](pi-1.png) - copy your API credentials: Public and Encryption keys.
2. [Screenshot 2](pi-2.png) - Configure webhook and authorization headers.
3. [Screenshot 1](pi-3.png) - Add your API credentials: Public and Encryption keys to the Woocommerce settings page for Payinvert.

== License ==

This plugin is licensed under the GPLv2 or later, just like WordPress itself. You can find the full text of the license [here](https://www.gnu.org/licenses/gpl-2.0.html).

== Contact ==

For support or questions, please contact us at [your@email.com].

== Donate ==

If you find this plugin helpful, consider [making a donation](https://example.com/donate) to support future development.
