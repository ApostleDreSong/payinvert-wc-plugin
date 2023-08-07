
jQuery(document).ready(function ($) {
  $('#regenerate_auth_header_value_button').on('click', function (e) {
    e.preventDefault(); // Prevent the default form submission
    var button = $(this);
    button.prop('disabled', true);
    // Make the AJAX request to trigger the form submission
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: { action: 'regenerate_auth_header_value' },
      success: function (response) {
        // Handle the response if needed
        console.log({ response });
        $('#woocommerce_payinvert_auth_header_value').val(response);
        button.prop('disabled', false);
      },
      error: function (xhr, status, error) {
        // Handle the error response
        console.error({ error });
      },
    });
  });
});
