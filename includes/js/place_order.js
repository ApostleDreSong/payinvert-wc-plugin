jQuery(document).ready(function ($) {
    // Find the "Place Order" button
    var placeOrderButton = $('button[name="woocommerce_checkout_place_order"]');
    
    // Add a click event listener to the "Place Order" button
    placeOrderButton.on('click', function (event) {
        event.preventDefault(); // Prevent the default form submission
        // Call the custom PHP function via AJAX
        $.ajax({
            type: 'POST',
            url: place_order_var.ajax_url, // Use the correct AJAX URL
            data: {
                action: 'process_payment' // This is the action name for the PHP function
            },
            success: function (response) {
                // Handle the AJAX response here, if needed
                console.log(response);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Find the "Place Order" button by its class
    var placeOrderButton = document.querySelector('.place-order');

    // Check if the button exists
    if (placeOrderButton) {
        // Remove the existing click event handler
        placeOrderButton.removeEventListener('click', wc_checkout_form.submit);

        // Add your custom function to be called when the button is clicked
        placeOrderButton.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Call your custom function here
            yourCustomFunction();
        });
    }
});