<?php
if (!class_exists('WC_Payment_Gateway')) {
    return;
}

class PayInvert extends WC_Payment_Gateway
{
    public function __construct()
    {
        // Define gateway properties
        $this->id = 'payinvert';
        $this->method_title = 'PayInvert Gateway';
        $this->method_description = 'Accept payments via the PayInvert Gateway.';
        $this->supports = array(
            'products',
            'refunds'
        );

        // Load settings
        $this->init_form_fields();
        $this->init_settings();

        // Define settings
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');

        // Additional settings for auth header
        $this->auth_header_name = $this->get_option('auth_header_name');
        $this->auth_header_value = $this->get_option('auth_header_value');
        $this->auth_header_value = empty($this->auth_header_value) ? $this->generate_auth_header_value() : $this->auth_header_value;
        $this->settings['auth_header_value'] = $this->auth_header_value;
        $this->settings['webhook_url'] = esc_attr($this->get_webhook_url());


        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        // Add the thank you page hook to load the iFrame
        add_action('woocommerce_thankyou_' . $this->id, array($this, 'order_received'), 10, 1);
        // Hook to handle incoming webhooks
        add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'webhook_listener'));
        // Add a filter to customize the gateway icon during initialization
        add_filter('woocommerce_gateway_icon', array($this, 'customize_gateway_icon'), 10, 2);


        add_action('wp_ajax_custom_place_order', 'process_payment'); // For logged-in users
        add_action('wp_ajax_nopriv_custom_place_order', 'process_payment'); // For non-logged-in users
    }

    // Function to customize the gateway icon
    public function customize_gateway_icon($icon_html, $payment_id)
    {
        // Check if the current payment method matches your gateway ID
        if ($payment_id === $this->id) {
            // Replace 'path_to_your_logo.png' with the actual path to your logo image
            $logo_url = plugin_dir_url(__FILE__) . './img/logo.png';

            // Customize the HTML for displaying the logo
            $icon_html = '<img width="25px" height="25px" src="' . $logo_url . '" alt="' . esc_attr($this->method_title) . '" class="payinvert-logo"/>';

            return $icon_html;
        }

        return $icon_html;
    }

    // Order Received
    public function order_received($order_id)
    {
        // Get the order object
        $order = wc_get_order($order_id);
        // Get the current date and time
        $currentDateTime = date('Y-m-d H:i:s');
        $order_reference = 'WP-WC-PI' . '_' . $order_id . '_' . $currentDateTime;
        if (isset($_GET['is_final_status']) && $_GET['is_final_status'] === 'true') {
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $status_code = isset($_GET['status_code']) ? $_GET['status_code'] : '';
            if ($status === 'success' && $status_code === '00') {
                // Update order status to "completed"
                $order->update_status('completed', __('Payment completed successfully.', 'payinvert-gateway'));
            }
        }
    }

    // Function to display the iFrame on the thank you page
    public function display_iframe($order_id)
    {
        $order = wc_get_order($order_id);
        // Get the order object
        // Get the current date and time
        $currentDateTime = date('Y-m-d H:i:s');
        $order_reference = 'WP-WC-PI' . '_' . $order_id . '_' . $currentDateTime;

        // Check if iFrame should be used based on the WooCommerce settings
        // Get the plugin settings
        $settings = get_option('woocommerce_' . $this->id . '_settings');
        // Get billing data from the order
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();
        $billing_phone = $order->get_billing_phone();
        $billing_email = $order->get_billing_email();
        $billing_amount = $order->get_total();
        $billing_country = $order->get_billing_country();
        // Get the API key from the plugin settings
        $api_key = isset($settings['api_key']) ? $settings['api_key'] : '';
        $encryption_key = isset($settings['encryption_key']) ? $settings['encryption_key'] : '';

        // Add the international dialing code if the phone number doesn't already start with it
        $international_dialing_code = '+234'; // Replace this with the desired international dialing code
        if (!empty($billing_phone) && strpos($billing_phone, $international_dialing_code) !== 0) {
            // Remove any non-digit characters from the phone number
            $billing_phone = preg_replace('/[^0-9]/', '', $billing_phone);

            // Prepend the international dialing code to the phone number
            $billing_phone = $international_dialing_code . $billing_phone;
        }
        ?>
        <?php
        $rest_nonce = wp_create_nonce('payinvert_update_order_nonce');
        wp_localize_script(
            'payinvert-gateway-functions',
            'my_var',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'security' => $rest_nonce,
                'orderId' => $order_id
            )
        );
        ?>
        <script>
            const payment_data = {
                'apiKey': '<?php echo $api_key; ?>',
                'firstName': '<?php echo $billing_first_name; ?>',
                'lastName': '<?php echo $billing_last_name; ?>',
                'country': '<?php echo $billing_country; ?>',
                'mobile': '<?php echo $billing_phone; ?>',
                'email': '<?php echo $billing_email; ?>',
                'amount': '<?php echo $billing_amount; ?>',
                'currency': '<?php echo get_woocommerce_currency(); ?>',
                'reference': '<?php echo $order_reference; ?>',
                'encryptionKey': '<?php echo $encryption_key; ?>',
                'onCompleted': onCompletedFunction,
                'onError': onErrorFunction,
                'onClose': onCloseFunction
            };
            const Pay = new window.PayinvertNS.Payinvert(payment_data);
            Pay.init();
        </script>
        <?php
        // ***************** //
        $response = array(
            'message' => 'agba coder on the ',
            'redirect_url' => $this->get_return_url($order),

        );

        // Send JSON response back to the JavaScript
        wp_send_json($response);
        // ************** //
        // Construct the JavaScript to load the iframe
        //     $iframe_script = '
        //     document.addEventListener("DOMContentLoaded", function() {
        //         const payment_data = {
        //             "apiKey": "' . $this->settings['api_key'] . '",
        //             "firstName": "' . $billing_first_name . '",
        //             "lastName": "' . $billing_last_name . '",
        //             "country": "' . $billing_country . '",
        //             "mobile": "' . $billing_phone . '",
        //             "email": "' . $billing_email . '",
        //             "amount": "' . $billing_amount . '",
        //             "currency": "' . get_woocommerce_currency() . '",
        //             "reference": "' . $order_reference . '",
        //             "encryptionKey": "' . $this->settings['encryption_key'] . '",
        //             "onCompleted": onCompletedFunction,
        //             "onError": onErrorFunction,
        //             "onClose": onCloseFunction
        //         };
        //         const Pay = new window.PayinvertNS.Payinvert(payment_data);
        //         Pay.init();
        //     });
        // ';

        //     // Output the iframe script
        //     // Enqueue the inline script
        //     wp_add_inline_script('payinvert-script', $iframe_script);
    }

    // Initialize settings fields
    public function init_form_fields()
    {

        $this->form_fields = array(
            'enabled' => array(
                'title' => 'Enable/Disable',
                'label' => 'Enable My Payment Gateway',
                'type' => 'checkbox',
                'description' => '',
                'default' => 'yes',
            ),
            'title' => array(
                'title' => 'Title',
                'type' => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default' => 'My Payment Gateway',
                'desc_tip' => true,
            ),
            'use_iframe' => array(
                'title' => 'Use iFrame',
                'label' => 'Use iFrame for payment',
                'type' => 'checkbox',
                'description' => 'Check this box to load the payment in an iFrame.',
                'default' => 'yes',
            ),
            'description' => array(
                'title' => 'Description',
                'type' => 'textarea',
                'description' => 'This controls the description which the user sees during checkout.',
                'default' => 'Pay securely using My Payment Gateway.',
                'desc_tip' => true,
            ),
            'api_key' => array(
                'title' => 'API Key',
                'type' => 'text',
                'description' => 'Enter your PayInvert API Key here.',
                'desc_tip' => true,
            ),
            'encryption_key' => array(
                'title' => 'Encryption Key',
                'type' => 'text',
                'description' => 'Enter your PayInvert Encryption Key here.',
                'desc_tip' => true,
            ),
            'webhook_url' => array(
                'title' => 'Webhook URL',
                'type' => 'text',
                'description' => '<strong>Optional:</strong> To avoid situations where a bad network makes it impossible to verify transactions, set your webhook URL to the URL below:<br>',
                'desc_tip' => true,
                'default' => '',
                // Use the dynamic webhook URL in the description
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                ),
            ),
            'auth_header_name' => array(
                'title' => 'Authorization Header Name',
                'type' => 'text',
                'description' => 'The name of the authorization header to be checked in the webhook listener.',
                'default' => 'webhook_auth',
                // Change this to the desired auth header name.
                'desc_tip' => true,
            ),
            'auth_header_value' => array(
                'title' => 'Authorization Header Value',
                'type' => 'text',
                'description' => 'The value for the authorization header. This will be auto-generated if left empty.',
                'desc_tip' => true,
                'default' => '',
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                ),
            ),
            'regenerate_auth_header_button' => array(
                'type' => 'custom_regenerate_auth_header_button',
            )
        );
    }

    // Function to get the webhook URL for completed payment notifications
    private function get_webhook_url()
    {
        // Construct the webhook URL using the WC API endpoint for this gateway
        $webhook_url = WC()->api_request_url('PayInvert');

        return $webhook_url;
    }

    // Function to check if the gateway is valid for use
    public function is_valid_for_use()
    {
        // Check if the currency is NGN
        if ('NGN' !== get_woocommerce_currency()) {
            return false;
        }

        return parent::is_valid_for_use();
    }

    // Process the payment and return the result
    public function process_payment($order_id)
    {

        $order = wc_get_order($order_id);

        // Get the plugin settings
        $settings = get_option('woocommerce_' . $this->id . '_settings');

        // Check if the API key is provided in the settings
        $api_key = isset($settings['api_key']) ? $settings['api_key'] : '';
        $encryption_key = isset($settings['encryption_key']) ? $settings['encryption_key'] : '';
        if (empty($api_key) || empty($encryption_key)) {
            // If the API key is not provided, show an error message and do not process the payment.
            wc_add_notice(__('Payment processing failed. Missing Keys.', 'payinvert-gateway'), 'error');
            return array(
                'result' => 'fail',
                'redirect' => wc_get_checkout_url(), // Redirect back to the checkout page.
            );
        }

        // Example 1: Load the payment in an iFrame using the provided iFrame script.
        if ($this->use_iframe()) {
            // Mark the order as "on-hold" to indicate that payment is being processed.
            $order->update_status('on-hold', __('Payment is being processed.', 'payinvert-gateway'));

            $this->display_iframe($order_id);

        } else {
            // Example 2: Redirect the customer to the payment checkout page.
            // Mark the order as "on-hold" to indicate that payment is being processed.
            $order->update_status('on-hold', __('Payment is being processed.', 'payinvert-gateway'));
            // Get the current date and time
            $currentDateTime = date('Y-m-d H:i:s');
            $order_reference = 'WP-WC-PI' . '_' . $order_id . '_' . $currentDateTime;
            // Get the plugin settings
            $settings = get_option('woocommerce_' . $this->id . '_settings');
            // Get billing data from the order
            $billing_first_name = $order->get_billing_first_name();
            $billing_last_name = $order->get_billing_last_name();
            $billing_phone = $order->get_billing_phone();
            $billing_email = $order->get_billing_email();
            $billing_country = $order->get_billing_country();

            // Get the API key from the plugin settings
            $api_key = isset($settings['api_key']) ? $settings['api_key'] : '';
            $encryption_key = isset($settings['encryption_key']) ? $settings['encryption_key'] : '';

            // Add the international dialing code if the phone number doesn't already start with it
            $international_dialing_code = '+234'; // Replace this with the desired international dialing code
            if (!empty($billing_phone) && strpos($billing_phone, $international_dialing_code) !== 0) {
                // Remove any non-digit characters from the phone number
                $billing_phone = preg_replace('/[^0-9]/', '', $billing_phone);

                // Prepend the international dialing code to the phone number
                $billing_phone = $international_dialing_code . $billing_phone;
            }

            // Construct the redirect URL with all available billing information
            $redirect_url = 'https://payment-checkout-dev.payinvert.com/?';
            $redirect_url .= 'reference=' . $order_reference;
            $redirect_url .= '&amount=' . $order->get_total();
            $redirect_url .= '&firstName=' . $billing_first_name;
            $redirect_url .= '&lastName=' . $billing_last_name;
            $redirect_url .= '&mobile=' . $billing_phone;
            $redirect_url .= '&country=' . $billing_country;
            $redirect_url .= '&email=' . $billing_email;
            $redirect_url .= '&option=' . ''; // Add any other options you need to pass
            $redirect_url .= '&currency=' . get_woocommerce_currency();
            $redirect_url .= '&encryptionKey=' . $encryption_key; // Add your encryption key if needed
            $redirect_url .= '&apiKey=' . $api_key;
            $redirect_url .= '&redirectUrl=' . $this->get_return_url($order);

            // Redirect the customer to the payment checkout page
            return array(
                'result' => 'success',
                'redirect' => $redirect_url,
            );
        }
    }

    // Function to check if the iFrame should be used based on the WooCommerce settings
    public function use_iframe()
    {
        // Get the plugin settings
        $settings = get_option('woocommerce_' . $this->id . '_settings');

        // Check if the 'use_iframe' setting exists and if it's set to 'yes'
        if (isset($settings['use_iframe']) && $settings['use_iframe'] === 'yes') {
            return true;
        }

        return false;
    }

    // Handle webhooks for completed orders
    // Webhook listener to handle completed payment notifications from the payment gateway
    public function webhook_listener()
    {
        // Retrieve the raw payload from the webhook request
        $raw_payload = file_get_contents('php://input');

        // Decode the JSON payload (assuming your payment gateway sends webhook data in JSON format)
        $payload = json_decode($raw_payload, true);



        if (empty($payload) || !isset($payload['Data']) || !isset($payload['Data']['OrderReference'])) {
            // Log or handle the error if payload is empty or missing OrderReference
            status_header(400);
            echo 'Empty Payload';
            exit;
        }

        // Extract the order ID from the OrderReference in the payload
        $order_reference = $payload['Data']['OrderReference'];

        // The order_reference contains "WP-WC-PI_12_2023-03-21"
        // Extract the order ID from the order_reference
        $order_id = explode('_', $order_reference)[1]; // This will give you "12"

        // Retrieve the WooCommerce order object
        $order = wc_get_order($order_id);
        if (!$order) {
            // Log or handle the error if the order is not found in WooCommerce
            status_header(400);
            echo 'No Order to process';
            exit;
        }
        // Get the merchant's registered email address
        $merchant_email = get_option('woocommerce_email_from_address');

        // Create the email content
        $subject = 'Webhook Notification';
        $message = "The webhook was called at " . date('Y-m-d H:i:s') . "\n";
        $message .= "Order ID: $order_id\n";
        $message .= "Amount: {$payload['Data']['TotalAmountCharged']}\n";
        $message .= "Order Status: {$payload['Status']}\n";
        $message .= "IP Address: {$_SERVER['REMOTE_ADDR']}\n";
        // Include other extracted data as needed

        // Send email notification
        error_log($message);
        wp_mail($merchant_email, $subject, $message);

        // Check if the authorization header is present in the webhook response headers
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (
                isset($headers[$this->auth_header_name]) &&
                $headers[$this->auth_header_name] === $this->auth_header_value
            ) {
                if (!isset($payload['StatusCode'])) {
                    status_header(400);
                    echo 'StatusCode not present';
                    exit;
                }
                ;
                $payment_success = isset($payload['StatusCode']) && $payload['StatusCode'] === '00';

                // If payment is successful, update the order status and note
                if ($payment_success) {
                    // Update the order status to "completed"
                    $order->update_status('completed', __('Payment successfully received.', 'payinvert-gateway'));

                    // Add a note to the order to record the payment
                    $order->add_order_note(__('Payment successfully received.', 'payinvert-gateway'));
                    echo 'Payment Successful';
                    exit;
                } else {
                    // If the payment is not successful, you may handle the failure accordingly.
                    // For example, you can update the order status to "failed" and add a note.
                    $order->update_status('failed', __('Payment failed.', 'payinvert-gateway'));
                    $order->add_order_note(__('Payment failed.', 'payinvert-gateway'));
                    echo 'Payment Failed';
                    exit;
                }
            } else {
                $order->update_status('on-hold', __('Payment webhook authorization failed.', 'payinvert-gateway'));
                $order->add_order_note(__('Payment webhook authorization failed.', 'payinvert-gateway'));
                echo 'Authorization failed';
                exit;
            }
        } else {
            echo 'Sorry. This request can not be processed at the moment';
            exit;
        }
    }

    // Function to add the "Regenerate Auth Header Value" button to the gateway settings
// Inside the 'generate_custom_regenerate_auth_header_button_html' function:
    public function generate_custom_regenerate_auth_header_button_html()
    {
        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <?php _e('Regenerate Auth Header Value', 'payinvert-gateway'); ?>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text">
                        <span>
                            <?php _e('Regenerate Auth Header Value', 'payinvert-gateway'); ?>
                        </span>
                    </legend>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="regenerate_auth_header_value">
                        <?php wp_nonce_field('regenerate_auth_header_nonce', 'regenerate_auth_header_nonce'); ?>
                        <button type="submit" class="button" id="regenerate_auth_header_value_button">
                            <?php _e('Regenerate', 'payinvert-gateway'); ?>
                        </button>
                    </form>
                </fieldset>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    public function enqueue_admin_scripts()
    {
        wp_enqueue_script('payinvert_gateway-admin', plugin_dir_url(__FILE__) . './js/admin.js', array('jquery'), '1.0', true);
        wp_localize_script(
            'payinvert-admin',
            'payinvert_ajax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('payinvert_generate_key_nonce'),
            )
        );
    }

    // Generate a random value for the authorization header
    private function generate_auth_header_value()
    {
        return bin2hex(random_bytes(16)); // Generate a random 32-character hex string
    }
}