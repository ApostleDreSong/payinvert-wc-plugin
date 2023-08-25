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
add_action('wp_ajax_update_status_of_order', 'update_status_of_order');
wp_enqueue_script('payinvert-script', 'https://gateway.payinvert.com/v1.0.0/payinvert.js', array(), null, true);
wp_enqueue_script('payinvert-gateway-functions', plugin_dir_url(__FILE__) . "includes/js/payinvert-gateway-functions.js", array('jquery'), null, true);
wp_enqueue_style('unique-style-handle', plugin_dir_url(__FILE__) . 'includes/css/pi.css', array(), '1.0.0', 'all');
wp_enqueue_script('place_order', plugin_dir_url(__FILE__) . 'includes/js/place_order.js', array('jquery'), '1.0', true);
wp_localize_script(
    'place_order',
    'place_order_var',
    array(
        'ajaxurl' => admin_url('admin-ajax.php')
    )
);

function regenerate_auth_header_value()
{
    $existingKey = get_option('auth_header_value');
    $newKey = bin2hex(random_bytes(16));
    update_option('auth_header_value', $newKey);
    echo $newKey;

    wp_die();
}

function update_status_of_order()
{
    // Retrieve the status and order ID from the AJAX request
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    $order_id = isset($_POST['order_id']) ? absint($_POST['order_id']) : '';

    if (empty($status) || !$order_id) {
        wp_send_json_error('Invalid request data');
    }
    $order = wc_get_order($order_id);
    if (!$order) {
        wp_send_json_error('Invalid order ID');
    }
    // Update the order status based on the received status
    switch ($status) {
        case 'completed':
            $order->update_status('completed', __('Payment completed successfully.', 'payinvert-gateway'));
            break;
        case 'failed':
            $order->update_status('failed', __('Payment failed.', 'payinvert-gateway'));
            break;
        // Add more cases to handle other potential status values as needed
        // For example, you might want to handle pending, on-hold, or processing statuses.
        default:
            // If the status received is not recognized, update the order status to 'on-hold'
            // or handle it according to your specific requirements.
            $order->update_status('on-hold', __('Payment status not recognized.', 'payinvert-gateway'));
            break;
    }
    echo 'Order status updated successfully';
    wp_die();
}