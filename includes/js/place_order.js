jQuery(document).ready(function ($) {
    // Attach a submit event handler to the WooCommerce checkout form
    $('form.checkout').on('submit', function (event) {
        event.preventDefault(); // Prevent the form from submitting
        alert('i receive it');
        $.ajax({
            type: 'POST',
            url: place_order_var.ajax_url, // Use the correct AJAX URL
            data: {
                action: 'process_payment_new' // This is the action name for the PHP function
            },
            success: function (response) {
                // Handle the AJAX response here, if needed
                console.log({ response });
                // window.location.href = place_order_var.orderReceivedUrl;
            },
            error: function (xhr, status, error) {
                // Handle the error response
                console.error({ error });
                // Redirect to checkout page with failure message
                const errorMessage = 'Checkout process failed. Please try again later.';
                window.location.href = place_order_var.checkoutUrl + '?checkout_error=' + encodeURIComponent(errorMessage);
            },
        });
    });
});