<?php
/**
 * Plugin Name: PayInvert
 * Description: PayInvert WooCommerce payment gateway that supports iFrame and redirect.
 * Version: 1.0.0
 * Author: Damilare ADEMESO
 */

// Prevent direct access to this file.
defined('ABSPATH') || exit;

// Add the gateway to WooCommerce

// Initialize the plugin
function my_payment_gateway_init()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    require_once('includes/gateway.php');

    add_filter('woocommerce_payment_gateways', 'add_my_payment_gateway');
}

add_action('plugins_loaded', 'my_payment_gateway_init');

function add_my_payment_gateway($gateways)
{
    $gateways[] = 'PayInvert';
    return $gateways;
}

add_action('wp_ajax_regenerate_auth_header_value', 'regenerate_auth_header_value');
wp_enqueue_script('payinvert-script', 'https://gateway.payinvert.com/v1.0.0/payinvert.js', array(), null, true);
wp_enqueue_script('payinvert-gateway-functions', plugin_dir_url(__FILE__) . "includes/js/payinvert-gateway-functions.js", array('jquery'), null, true);
wp_enqueue_style('unique-style-handle', plugin_dir_url(__FILE__) . 'includes/css/pi.css', array(), '1.0.0', 'all');

function regenerate_auth_header_value()
{
    $existingKey = get_option('auth_header_value');
    $newKey = bin2hex(random_bytes(16));
    update_option('auth_header_value', $newKey);
    echo $newKey;

    wp_die();
}