jQuery(document).ready(function ($) {
    // Attach a submit event handler to the WooCommerce checkout form
    $('form.checkout').on('submit', function (event) {
        event.preventDefault(); // Prevent the form from submitting
        alert('olorun 1930')
        $.ajax({
            type: 'POST',
            url: place_order_var.ajax_url, // Use the correct AJAX URL
            data: {
                action: 'process_payment' // This is the action name for the PHP function
            },
            success: function (response) {
                // Handle the AJAX response here, if needed
                console.log({response});
            },
            error: function (xhr, status, error) {
                // Handle the error response
                console.error({ error });
              },
        });
    });
});