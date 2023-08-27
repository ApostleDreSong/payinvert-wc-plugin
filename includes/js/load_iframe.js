jQuery(document).ready(function ($) {
    $('#place_order').off('submit').on('click', function (event) {
        event.preventDefault(); // Prevent the default form submission
        //   var button = $(this);
        //   button.prop('disabled', true);
        alert('we reach here o')
        // Make the AJAX request to trigger the form submission
        //   $.ajax({
        //     type: 'POST',
        //     url: ajaxurl,
        //     data: { action: 'loadIframe' },
        //     success: function (response) {
        //       // Handle the response if needed
        //       console.log({ response });
        //     },
        //     error: function (xhr, status, error) {
        //       // Handle the error response
        //       console.error({ error });
        //     },
        //   });
    });
});