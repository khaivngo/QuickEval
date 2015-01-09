$(document).ready(function() {
    $('#nav-right-buttons').append('<button class="element place-right bg-darker" onclick="register()">Register</button>');
    $('#nav-right-buttons').append('<button class="element place-right" onclick="login()">Login</button>');

    getInstitutions();

    $('#email').focusout(function() {
        $('#notify').remove();
        if (!checkUserEmail()) {
            $('#email-div').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                    'Already exists' +
                    '</div>');
        }
    })

    $('#submit').click(function() {
        var passwordEncrypted = CryptoJS.SHA3($('#password').val()).toString();
        $('#notify').remove();
        if (checkValues() && checkFieldLength()) {
            registerUser(passwordEncrypted);
        }
    });
});

/**
 * Gets all institutes and appends them to institute list
 */
function getInstitutions() {
    $.ajax
            ({
                url: 'ajax/observer/getInstitutions.php',
                async: false,
                dataType: 'json',
                success: function(data)
                {
                    for (var i = 0; i < data.length; i++) {
                        $('#institution').append('<option id="institution"' + i + '>' + data[i]["name"] + '</option>');
                    }
                },
                error: function(request, status, error) {
                    alert(request.responseText);
                }
            });
}

/**
 * Checks the values in the fields.
 * Checks email, passwords to match and date, checks if email is taken
 * @returns {Boolean}
 */
function checkValues() {
    var age = (new Date().getFullYear() - ($('#calendar').val()));  //age in years
    var password = $('#password').val();
    
    if (!validEmail($('#email').val())) {
        $('#email-div').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                'Invalid Email' +
                '</div>');
        return false;
    }

    if (age < 8 || age > 110) {     //Must be between 8 and 110 years of age
        $('#calendar-div').after('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Invalid Year of Birth' +
                '</div></div>');
        return false;
    }

    if (password.length <= 4) {
        $('#password-div').after('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Password needs to be longer than 4 characters' +
                '</div></div>');
        return false;
    }

    if ($('#password').val() !== $('#password2').val()) {
        $('#password2-div').after('<div id="notify">' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Passwords do not match' +
                '</div></div>');
        return false;
    }

    if (!checkUserEmail()) {
        $('#email-div').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                'Already exists' +
                '</div>');
        return false;
    }

    return true;
}

/**
 * Checks the length on the values in the fields.
 * Also checks the dropdown menus for "select" not to be selected
 * @returns {Boolean}
 */
function checkFieldLength() {

    var field;

    if ($('#email').val().length < 4) {
        field = $('#email-div');
    }
    if ($('#password').val().length < 4) {
        field = $('#password-div');
    }
    if ($('#password2').val().length < 4) {
        field = $('#password2-div');
    }
    if ($('#first-name').val().length < 2) {
        field = $('#first-name-div');
    }
    if ($('#last-name').val().length < 2) {
        field = $('#first-name-div');
    }
    if ($('#calendar').val().length < 4 || $('#calendar').val().length > 4) {  //Needs span
        $('#calendar-div').after('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Invalid Date of Birth' +
                '</div></div>');
        return false;
    }
    if ($('#sex').val() == "Select") {
        field = $('#calendar-div');
    }
    if ($('#institution').val() == "Select") {
        field = $('#institution-div');
    }

    if (field) {
        field.after('<div id="notify">' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Invalid field length or value' +
                '</div></div>');
        return false;
    }

    return true;
}

/**
 * Checks if the string is an valid email address
 * @param {type} email the string to be matched
 * @returns {Boolean}
 */
function validEmail(email) {
    var filter = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    return String(email).search(filter) !== -1;
}

/**
 * Registers user and displays a message if registration succeeds/fails
 * @param {type} $password
 * @returns {undefined}
 */
function registerUser($password) {

    $.ajax
            ({
                url: 'ajax/observer/registerUser.php',
                async: false,
                data: {'email': $('#email').val(),
                    'password': $password,
                    'firstName': $('#first-name').val(),
                    'lastName': $('#last-name').val(),
                    'sex': $('#sex').val(),
                    'age': $('#calendar').val(),
                    'institution': $('#institution').val(),
                    'nationality': $('#nationality').val()},
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (!checkUserEmail()) {
                        $.Notify({
                            content: "Registration successful!"
                        });
                        setTimeout(function() {
                            window.location = 'index.php';
                        }, 3000);
                    } else {
                        $.Notify({
                            content: "Registration failed!"
                        });
                    }

                },
                error: function(request, status, error) {

                }
            });
}

/**
 * Returns true if the username is available
 * @returns {undefined}
 */
function checkUserEmail() {

    var check;

    $.ajax
            ({
                url: 'ajax/observer/checkUserEmail.php',
                async: false,
                data: {'email': $('#email').val()},
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    (data > 0) ? check = false : check = true;
                },
                error: function(request, status, error) {

                }
            });
    return check;
}




