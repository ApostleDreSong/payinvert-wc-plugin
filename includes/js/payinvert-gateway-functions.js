function onCompletedFunction(data) {
    jQuery.ajax({
        method: 'POST',
        dataType: 'json',
        url: my_var.ajaxurl,
        data: {
            action: 'update_status_of_order',
            order_id: my_var.orderId,
            status: 'completed'
        },
        success: function (response) {
            if (response && response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
            }
            // You can handle the response here if needed
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
            // Handle the error here if the AJAX call fails
        }
    });
};

function onErrorFunction(error) {
    // Payment failed
    // Update order status to "failed"
    // Define the data to be sent as JSON

    // Make the AJAX request using jQuery.ajax
    jQuery.ajax({
        method: 'POST',
        dataType: 'json',
        url: my_var.ajaxurl,
        data: {
            action: 'update_status_of_order',
            order_id: my_var.orderId,
            status: 'failed'
        },
        success: function (response) {
            if (response && response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
            }
            // You can handle the response here if needed
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
            // Handle the error here if the AJAX call fails
        }
    });
};

function onCloseFunction() {
    jQuery.ajax({
        method: 'POST',
        dataType: 'json',
        url: my_var.ajaxurl,
        data: {
            action: 'update_status_of_order',
            order_id: my_var.orderId,
            status: 'closed'
        },
        success: function (response) {
            if (response && response.data.redirect_url) {
                window.location.href = response.data.redirect_url;
            }
            // You can handle the response here if needed
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
            // Handle the error here if the AJAX call fails
        }
    });
};

