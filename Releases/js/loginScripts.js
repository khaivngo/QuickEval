$(document).ready(function() {
    $('#nav-right-buttons').append('<button class="element place-right"><a href="register.php">Register</a></button>');
    $('#nav-right-buttons').append('<button class="element place-right"><a href="login.php">Login</a></button>');

    $('#submit').click(function() {
        $('#notify').remove();
        loginUser();
    });

    $('#anonymous').click(function() {
        loginAnonymous();
    });

});

function loginAnonymous() {
    $.ajax
            ({
                url: 'ajax/observer/loginAnonymous.php',
                async: false,
                dataType: 'json',
                success: function(data) {


                },
                error: function(request, status, error) {
                    alert(request.responseText);
                }
            });
}

function loginUser() {
    $.ajax
            ({
                url: 'ajax/observer/userSession.php',
                async: false,
                data: {'email': $('#email').val(),
                    'password': CryptoJS.SHA3($('#password').val()).toString()},
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (data != "0") {
                        window.location = 'index.php';

                    } else {
                        $('#password-div').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                                'Invalid email or password' +
                                '</div>');
                    }

                },
                error: function(request, status, error) {
                    alert(request.responseText);
                }
            });
}

