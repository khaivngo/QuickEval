$(document).ready(function() {
    $('#nav-right-buttons').append('<button class="element place-right" onclick="register();">Register</button>');
    $('#nav-right-buttons').append('<button class="element place-right bg-darker" onclick="login();">Login</button>');

    $('#submit').click(function() {
        $('#notify').remove();
        loginUser();
    });

    $('#anonymous').click(function() {
        loginAnonymous();
    });

    $('.form-remove').submit(function(e) {
        e.preventDefault();
    });



    //Checks whether the user shall be logged in as anonymous automatically.
    autoLogin(1);
    if(localStorage.autoLoginCheck1 == "true")    {
        localStorage.autoLoginCheck1 = false;
        loginAnonymous();
    }

});

function loginAnonymous() {
    $('#anonymous, #submit').prop('disabled', true);
                //console.log('beforewindow');
                //console.log('beforewindow');

    $.ajax({
        url: 'ajax/observer/loginAnonymous.php',
        async: false,
        dataType: 'json',
        success: function(data) {
            $('#anonymous,#submit').prop('disabled', false);

            if ($('#redirect').val() != undefined) {
                window.location = $('#redirect').val();
            } else {
                window.location = 'index.php';
            }
        },
        error: function(request, status, error) {
            console.log(request.responseText);

            $('#anonymous,#submit').prop('disabled', false);
        }
    });
}

function loginUser() {
    $('#anonymous, #submit').prop('disabled', true);

    $.ajax({
        url: 'ajax/observer/userSession.php',
        async: false,
        data: {
            'email': $('#email').val(),
            'password': CryptoJS.SHA3($('#password').val()).toString(),
        },
        type: 'post',
        dataType: 'json',
        success: function(data) {
            console.log(data);
            if (data == "0") {
                $('#password-div').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                    'Invalid email or password' +
                    '</div>');

            } else {
                if ($('#redirect').val() != undefined) {
                    window.location = $('#redirect').val();
                } else {

                    window.location = 'login.php';
                }
            }
            $('#anonymous,#submit').prop('disabled', false);
        },
        error: function(request, status, error) {
            console.log(request.responseText);
            alert("Something went wront, please try again.");
            $('#anonymous,#submit').prop('disabled', false);
        }
    });
}