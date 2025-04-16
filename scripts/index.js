$(document).ready(function() {
    $('#feedbackForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Collect form data
        let formData = {
            name: $('input[name="name"]').val(),
            email: $('input[name="email"]').val(),
            rating: $('input[name="rating"]').val(),
            feedbackText: $('textarea[name="feedbackText"]').val()
        };

        // ✅ Print form data in console
        console.log("Form Data Submitted:", formData);

        // AJAX request
        $.ajax({
            url: './feedback-handler.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#responseMessage').html('<p style="color: green;">' + response + '</p>');
                $('#feedbackForm')[0].reset(); // Clear form
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html('<p style="color: red;">Error: ' + error + '</p>');
                console.error("AJAX Error:", error);
            }
        });
    });
});
