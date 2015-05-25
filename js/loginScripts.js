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

var resetCheck = false;
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

                if(resetCheck == false) {
                    $('#submit').after('<br><br><button class="button info" id="forgot-password">Reset password</button>');
                    resetPasswordListener();
                    resetCheck = true;
                }


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

/**
 * Sets a listener to the appended button for resetting password.
 */
function resetPasswordListener()   {
    $('#forgot-password').click(function() {
        console.log('reset password');
        sendNewPassword();
    });
}

/**
 * Goes to the procedure of giving the user a new password.
 */
function sendNewPassword()  {
    var string = Math.random().toString(36).slice(-8);
    var email = prompt("Enter your email registered with QuickEval", "Email");

    updatePassword(string, email);

    $.ajax({
        url: 'ajax/observer/sendNewPassword.php',
        data: {
            'data': CryptoJS.SHA3(string).toString(),
            'string': string,
            'email': email,
        },
        type: 'POST',
        async: false,
        dataType: 'JSON',
        success: function(data) {
            console.log("success")
        },
        error: function(request, status, error) {
            console.log(request.responseText);
        }
    });
}

/**
 * Updates password.
 * @param string word.
 * @param email users mail.
 */
function updatePassword(string, email)   {
        $.ajax({
            type: 'POST',
            url: 'ajax/observer/updatePassword.php',
            data: {
                'password': CryptoJS.SHA3(string).toString(),
                'check': "newPassword",
                'email': email
            }, //crypts new password
            success: function (data) {
                $.Notify({//notifies user about successfull password change
                    content: "New password sent to mail",
                    style: {background: 'lime'},
                });
                location.reload();
            },
            error: function (request, status, error) {
                alert(request.responseText);
                console.log(request.responseText);
            }
        });
}
