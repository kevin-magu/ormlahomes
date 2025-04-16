$(document).ready(function () {
    $('#userForm').on('submit', function (e) {
        e.preventDefault(); // Prevent normal form submission

        var username = $('username').val();
        var password = $('#password').val();

        var formData = {
            username: username,
            password: password
        };

        console.log("Sending:", formData);

        $.ajax({
            url: 'push.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
            $('#response').html(response)
                
            },
          
        });
    });
});
