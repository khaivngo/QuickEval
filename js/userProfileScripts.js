$(document).ready(function () {

    inject("ajax/observer/editUserInfo.html"); 			//Starts on dashboard-page on refresh

    setUpModeMenu('.observer-mode');

    $("#register-date").datepicker({
        effect: "slide",
        selected: function (d, d0) {
            $('#calendar').val(d);  //Adds the selected value to the input field
        }
    });

    $("#edit-info").click(function () {
        inject("ajax/observer/editUserInfo.html");
        setActive($(this));

        fillUserInfo();

        $("#register-date").datepicker({
            effect: "slide",
            selected: function (d, d0) {
                $('#calendar').val(d);  //Adds the selected value to the input field
            }
        });

        $('#user-info-submit').click(function () {	//when user submits user info
            if (checkValuesUserInfo()) {           // checks if it's valid
                updateUserInfo();                  // if so, updates user's info
            }
        });


    });

    $("#change-password").click(function () {
        inject("ajax/observer/changePassword.html");

        setActive($(this));

        $('#password-submit').click(function () {	//when user submits new password

            if (checkValuesPassword()) {           // checks if it's valid
                console.log("PASS PASS")

                updatePassword();                  // if so, updates user's password
            }
            else {
                console.log("not valid")

            }
        });
    });

    $("#change-email").click(function () {
        inject("ajax/observer/changeEmail.html");

        var email = getUserSession('email');	//fetches user email from session
        $("#email").val(email);				//fills the field

        setActive($(this));

        $('#email-submit').click(function () {	//when user submits new email
            if (checkValuesEmail()) {				//	checks if it's valid
                updateEmail();					//	if so, updates user's email address
            }
        });
    });

    $("#style-settings").click(function () {
        inject("ajax/observer/styleSetting.html");
        setActive($(this));

        $('#style-submit').click(function () {                   //when user submits new background colour
            updateBackgroundColour();					//	if so, updates user's email address
        });
    });
});


function fillUserInfo() {
    var firstName = getUserSession('firstName');	//fetches user info from session
    $("#first-name").val(firstName);				//fills the field

    var lastName = getUserSession('lastName');
    $("#last-name").val(lastName);

    var nationality = getUserSession('nationality');
    $("#nationality").val(nationality);

    var title = getUserSession('title');
    $("#title").val(title);

    var phone = getUserSession('phoneNumber');
    $("#phone-number").val(phone);

}

/**
 * Injects a html-page into the right panel div.
 * $data is location of html file.
 */
function inject($data) {

    $.ajax({
        async: false,
        url: $data,
        success: function (data) {
            $($('#right-panel')).html(data);
        }
    });
}

/**
 * Sets the $target button active in the left
 * sidebar.
 * $data is the jquery element to be set active.
 */
function setActive($target) {
    $target.siblings().removeClass("active");
    $target.addClass("active");
}

//-----------------EMAIL-----------------------------------------------------------------------------------------------------------------

/**
 * Checks wether the two emails matches.
 */
function correctEmail() {
    var alike;
    ($('#email2').val() == $('#email3').val()) ? alike = true : alike = false;
    return alike;
}

/**
 * Checks if the email is valid
 * @param {string} email the email to check.
 */
function validEmail(email) {
    var filter = /^\s*[\w\-\+_]+(\.[\w\-\+_]+)*\@[\w\-\+_]+\.[\w\-\+_]+(\.[\w\-\+_]+)*\s*$/;
    return String(email).search(filter) !== -1;
}

/**
 * Checks if new email is a valid email.
 * Checks if new email and confirm new email matches.
 * Checks if new email already is in used in database.
 * @returns {Boolean} wether all the checks where passed.
 */

function checkValuesEmail() {            //checks wether the old and new email is correct and that the third matches the new one

    if (!validEmail($('#email3').val())) {
        $.Notify({
            content: "Invalid email",
            style: {background: 'red'},
            timeout: '6000'
        });
        return false;
    }

    if (!correctEmail()) {
        $.Notify({
            content: "Email's do not match",
            style: {background: 'red'},
            timeout: '6000'
        });
        return false;
    }

    if (!checkUserEmail()) {
        $.Notify({
            content: "Email already exists",
            style: {background: 'red'},
            timeout: '6000'
        });
        return false;
    }

    return true;
}

/**
 * Returns true if the username is available
 * @returns wether the email already exists or not
 */
function checkUserEmail() {
    var check;
    $.ajax
    ({
        url: 'ajax/observer/checkUserEmail.php',
        async: false,
        data: {'email': $('#email3').val()},
        type: 'post',
        dataType: 'json',
        success: function (data) {
            (data > 0) ? check = false : check = true;
        },
        error: function (request, status, error) {

        }
    });
    return check;
}

/**
 * Sends data to updataEmail.php which updates the database with new email
 */
function updateEmail() {
    $.ajax({
        type: 'POST',
        url: 'ajax/observer/updateEmail.php',
        data: {
            'email': $('email').val(),
            'email2': $('#email2').val(),
            'email3': $('#email3').val()
        },
        success: function (data) {
            $.Notify({//notifies user about successfull email change
                content: "Email updated",
                style: {background: 'lime'},
            });
        },
        error: function (request, status, error) {
            alert(request.responseText);
            console.log(request.responseText);
        }
    });
}


//-----------------PASSWORD--------------------------------------------------------------------------------------------------------------

/**
 * Checks if assumed old password actually matches the old password.
 * Checks if new password is longer than four characters.
 * Checks if the two new password and confirm new password matches.
 * @returns {Boolean} if all the checks where cleared
 */
function checkValuesPassword() {
    var sessionPassword = getUserSession('password');               //fetches user password from session
    var oldPassword = CryptoJS.SHA3($('#password').val()).toString();      //crypts assumed old password from user.

    var newPassword = $('#password2').val();            //fetching value of new password
    var confirmNewPassword = $('#password3').val();     //fetching value of confirm new password

    if (!(sessionPassword == oldPassword)) {     //assumed old password actually matches old passwor
        $("#password-div .notify-no-match").remove();
        $('#password').after('<div id="notify" class="notify-no-match">' +
            '<div class="span3" style="margin: 0 20px"></div>' +
            '<div class="bg-red notice marker-on-top span1">' +
            'Password does not match current password' +
            '</div></div>');
        return false;
    }

    if (newPassword.length <= 4) {                  //checks if length of new password is valid
        $("#password-div .notify-no-length").remove();
        $('#password2').after('<div id="notify" class="notify-no-length">' +
            '<div class="span3 notify-no-length" style="margin: 0 20px"></div>' +
            '<div class="bg-red notice marker-on-top span1">' +
            'Password needs to be longer than 4 characters' +
            '</div></div>');
        return false;
    }

    if (!(newPassword == confirmNewPassword)) {         //if new password and confirm new password matches.
        $("#password-div .notify-not-matching").remove();
        $('#password3').after('<div id="notify" class="notify-not-matching">' +
            '<div class="span3 notify-not-matching" style="margin: 0 20px"></div>' +
            '<div class="bg-red notice marker-on-top span1">' +
            'Passwords do not match' +
            '</div></div>');
        console.log("Both passwords do not match");
        return false;
    }

    return true;
}

/**
 * Sends data to updatePassword.php for updating data in database
 */
function updatePassword() {
    $.ajax({
        type: 'POST',
        url: 'ajax/observer/updatePassword.php',
        data: {
            'password': CryptoJS.SHA3($('#password2').val()).toString(),
            "check": "newPassword"
        }, //crypts new password
        success: function (data) {
            $.Notify({//notifies user about successfull password change
                content: "Password changed",
                style: {background: 'lime'},
            });
        },
        error: function (request, status, error) {
            // alert(request.responseText);
            console.log(request.responseText);
        }
    });
}


//-----------------USER INFO--------------------------------------------------------------------------------------------------------------
/**
 * Checks validity of input from user and if any of the fields have been updated.
 * @returns {Boolea} whether any fields were to short/invalid
 */
function checkValuesUserInfo() {

    console.log("Inside UpdateValuesUserInfo");

    var firstName = $('#first-name').val();
    var sessionFirstName = getUserSession('firstName');

    var lastName = $('#last-name').val();
    var sessionLastName = getUserSession('lastName');

    var nationality = $('#nationality').val();
    var sessionNationality = getUserSession('nationality');

    var title = $('#title').val();
    var sessionTitle = getUserSession('title');

    var phoneNumber = $('#phone-number').val();
    var sessionPhoneNumber = getUserSession('phoneNumber');

    //checking if there is any new input
    if (firstName != sessionFirstName) {
        if (firstName.length <= 1) {
            $('#first-name').after('<br><div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Too short' +
                '</div></div>');
            return false;
        }
    }
    //checking if there is any new input and length
    if (lastName != sessionLastName) {
        if (lastName.length <= 1) {
            $('#last-name').after('<br><div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Too short' +
                '</div></div>');
            return false;
        }
    }
    //checking if there is any new input and length
    if (nationality != sessionNationality) {
        if (nationality.length <= 3) {
            $('#nationality').after('<br><div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Too short' +
                '</div></div>');
            return false;
        }

    }
    //checking if there is any new input and length
    if (title != sessionTitle) {
        if (firstName.length <= 1) {
            $('#title').after('<br><div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Too short' +
                '</div></div>');
            return false;
        }
    }
    //checking if there is any new input and length
    if (phoneNumber != sessionPhoneNumber) {
        if (firstName.length <= 3) {
            $('#phone-number').after('<br><div id="notify"><div class="span3" style="margin: 0 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Too short' +
                '</div></div>');
            return false;
        }
    }
    //if everything is unchanged
    if (firstName == sessionFirstName && lastName == sessionLastName && nationality == sessionNationality && title == sessionTitle && phoneNumber == sessionPhoneNumber) {
        $.Notify({
            content: "No new info",
            style: {background: 'red'},
        });
        return false;           //do nothing
    }
    else {
        return true;            //all or some information has been updated.
    }
}


/**
 * Sends data to updateUserInfo.php for updating data in database
 */
function updateUserInfo() {
    $.ajax({
        type: 'POST',
        url: 'ajax/observer/updateUserInfo.php',
        data: {
            'firstName': $('#first-name').val(),
            'lastName': $('#last-name').val(),
            'nationality': $('#nationality').val(),
            'title': $('#title').val(),
            'phoneNumber': $('#phone-number').val()
        },
        success: function (data) {
            $.Notify({//notifies user about successfull email change
                content: "User info updated",
                style: {background: 'lime'},
            });
        },
        error: function (request, status, error) {
            alert(request.responseText);
            console.log(request.responseText);
        }
    });
}





