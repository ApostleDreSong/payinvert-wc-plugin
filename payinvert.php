<?php
/**
 * Plugin Name: PayInvert
 * Description: PayInvert WooCommerce payment gateway that supports iFrame and redirect.
 * Version: 1.0.0
 * Author: Damilare ADEMESO
 */

// Prevent direct access to this file.
defined( 'ABSPATH' ) || exit;

// Initialize the plugin
function my_payment_gateway_init() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
        return;
    }

    require_once( 'includes/gateway.php' );

    add_filter( 'woocommerce_payment_gateways', 'add_my_payment_gateway' );
}

add_action( 'plugins_loaded', 'my_payment_gateway_init' );

// function enqueue_payinvert_scripts() {
//     // Enqueue the 'payinvert-ns' script with a lower priority (e.g., 5).
//     wp_enqueue_script('payinvert-ns', 'https://gateway-dev.payinvert.com/v1.0.0/payinvert.js', array(), null, true);
// }
// add_action('wp_enqueue_scripts', 'enqueue_payinvert_scripts', 5);

// Add the gateway to WooCommerce
function add_my_payment_gateway( $gateways ) {
    $gateways[] = 'PayInvert';
    return $gateways;
}
