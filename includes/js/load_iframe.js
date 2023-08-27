jQuery(document).ready(function ($) {
    $('body').on('click', 'button[name="woocommerce_checkout_place_order"]', function (e) {
        // Prevent the default redirection
        e.preventDefault();

        // Show an alert
        alert('Thank you for your order!');

        // Optionally, you can submit the form programmatically
        // Make the AJAX request to trigger the form submission
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: { action: 'loadIframe' },
            success: function (response) {
                // Handle the response if needed
                console.log({ response });
            },
            error: function (xhr, status, error) {
                // Handle the error response
                console.error({ error });
            },
        });
        // $('form.checkout').submit();
    });
});