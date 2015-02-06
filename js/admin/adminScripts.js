
//--------------------------------CHANGE ACCESS-----------------------------------------//


/**
 * Sets up listeners to submit button and email-input-field
 * @returns {undefined}
 */
function userAccessListeners() {

    $('#submit-change-access').click(function() {
        $(document.body).on('click', '#submit-change-access', function() {
        });

        var email = $('#change-access-user').val();
        if (validEmail(email) && !checkUserEmail(email)) {  //If email is valid, and exists
            updateUserAccess($('#change-access-user').val(), $('#change-access-type').val());
        } else {
            delay(function() {            //if email is not valid
                $('.notice').remove();
                $('#change-access-user').after('<div id="notify"><div class="span1" style="margin: 20px"></div>' +
                        '<div class="bg-red notice marker-on-top span1">' +
                        'Not valid email' +
                        '</div></div>');
//                delay(function() {                      //removes feedback
//                    $('#notify').fadeOut();
//                }, 1500);
            }, 1000);
        }
    });
    //declaring delay, delay in milliseconds
    var delay = (function() {
        var timer = 0;
        return function(callback, ms) {
            clearTimeout(timer);
            timer = setTimeout(callback, ms);
        };
    })();
    $('#change-access-user').keyup(function() {      //Gets current access of user when email is typed
        loadUsers();
        $('.notice').remove();
        if (validEmail($(this).val())) {            //if the email is valid

            delay(function() {                      //delays the check if email exists and notifies user if not
                if (checkUserEmail($('#change-access-user').val())) {
                    $('#change-access-user').after('<div id="notify"><div class="span3" style="margin: 20px"></div>' +
                            '<div class="bg-red notice marker-on-top span4">' +
                            'Email not associated with any user' +
                            '</div></div>');
//                    delay(function() {                      //removes feedback
//                        $('#notify').fadeOut();
//                    }, 1500);
                } else {            //if email exists, get the owners current access level
                    console.log("email was found");
                    getCurrentUserAccess($('#change-access-user').val());
                }
            }, 150);
        }
    });


    $('#change-access-user').mouseup(function() {      //Gets current access of user when email is typed
        loadUsers();
        $('.notice').remove();
        if (validEmail($(this).val())) {            //if the email is valid

            delay(function() {                      //delays the check if email exists and notifies user if not
                if (checkUserEmail($('#change-access-user').val())) {
                    $('#change-access-user').after('<div id="notify"><div class="span3" style="margin: 20px"></div>' +
                    '<div class="bg-red notice marker-on-top span4">' +
                    'Email not associated with any user' +
                    '</div></div>');
//                    delay(function() {                      //removes feedback
//                        $('#notify').fadeOut();
//                    }, 1500);
                } else {            //if email exists, get the owners current access level
                    console.log("email was found");
                    getCurrentUserAccess($('#change-access-user').val());
                }
            }, 150);
        }
    });


}

/**
 * Retrieves names of available access levels from database and fills dropdown menu
 * @returns {undefined}
 */
function getAccessNames() {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/getAccessLevels.php',
        data: {},
        dataType: 'json',
        success: function(data) {
            $('#change-access-type').append('<option value="" disabled selected>Select access level</option>');
            for (var i = 0; i < data.length; i++) {
                $('#change-access-type').append('<option>' + data[i]['title'] + '</option>');
            }
        },
        error: function(request, status, error) {
            // alert(request.responseText);
            console.log(request.responseText);
        }
    });
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
 * Gets user access of the user with email=$email
 * Selects the access level based on the user
 * @param {type} $email
 * @returns {undefined}
 */
function getCurrentUserAccess($email) {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/getUserAccessLevel.php',
        data: {'email': $email},
        dataType: 'json',
        success: function(data) {
            $('#change-access-type').val(data[0][0]);
            console.log("Fetched usertype = "+data[0][0]);
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Updates user-access on user with email $email to type $type
 * @param {type} $email Email of user to get access changed
 * @param {type} $type Access type for user to be changed to
 * @returns {undefined}
 */
function updateUserAccess($email, $type) {
    if ($type == 'Observer') {
        userAccessUnpublishExperiments($email)
    }
    else {
        updateAccessLevel($email, $type);
    }
}

/**
 * Updates users access level based on email and type of access level
 * @param {type} $email Email address of user admin wish to edit access level
 * @param {type} $type  Type of access level.
 * @returns {undefined}
 */
function updateAccessLevel($email, $type) {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/updateUserAccessLevel.php',
        data: {'email': $email,
            'type': $type},
        success: function(data) {       //notifies which user was updated with what level
            $.Notify({//notifies user about successfull email change
                content: "User: " + $email + " updated to access level '" + $type + "'",
                style: {background: 'lime'},
            });
        },
        error: function(request, status, error) {
            alert(request.responseText);
            console.log("Error ajax");
        }
    });
}

/**
 * Checks if user exists based on email address
 * @param {type} $email Email address used for check if exists
 * @returns {Boolean} Whether user with that particular email exists
 */
function checkUserEmail($email) {
    var check;
    $.ajax({
        url: 'ajax/observer/checkUserEmail.php',
        async: false,
        data: {'email': $email},
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

/**
 * Unpublishes for a user who got demoted to observer
 * @param {type} $email Email address for user to get his experiments removed
 * @returns {undefined}
 */
function userAccessUnpublishExperiments($email) {
    $.ajax({
        url: 'ajax/admin/demoteUser.php',
        async: false,
        data: {'email': $email},
        type: 'post',
        datatype: 'json',
        success: function(data) {
            $.Notify({//notifies user about successfull email change
                content: "User: " + $email + " updated to access level, and " + data + " belonging experiments unpublished",
                style: {background: 'lime'},
            });
            updateAccessLevel($email, $('#change-access-type').val()); //updates access level of chosen user
        },
        error: function(request, status, error) {

        }
    });
}

//--------------------------------DELETE IMAGES---------------------------------------------------------------------------------//

/**
 * Sets up listeners for submit buttons and input fields
 * @returns {undefined}
 */
function deleteImagesListeners() {
    $('#submit-delete-images').click(function() {       //Deletes images on first submit button
        deleteImages();
    });
    $('#submit-delete-images-user').click(function() {  //Deletes images based on users on seconds submit button
        deleteImagesUser();
    });
    $('#delete-images-user').click(function() {          //Removes notice on click on input-field
        $('.notice').fadeOut(300, function() {
            $(this).remove();
        });
    });
    $(document.body).on('click', '.notice', function() { //Removes notice on notice-click
        $(this).fadeOut(300, function() {
            $(this).remove();
        });
    });
    $("#delete-images-date").datepicker({//Initializes datepicker
        effect: "slide",
        selected: function(d, d0) {
            $('#delete-images-datepicker').val(d); //Adds the selected value to the input field
        }
    });
}

/**
 * Deletes image-sets based on input from user
 * @returns {undefined}
 */
function deleteImages() {
    var date;
    if ($('#delete-images-datepicker').val() == "") {
        date = getDate($('#delete-images-select').val());
    } else {
        date = $('#delete-images-datepicker').val();
    }

    deleteImageSets(date);
}

/**
 * Performs the query to delete image-sets not used in experiments 
 * based on $date
 */
function deleteImageSets($date) {

}

/**
 * Checks if user exists and performs query to delete images based on user
 * @returns {undefined}
 */
function deleteImagesUser() {
    var email = $('#delete-images-user').val();
    var force = $('#delete-images-checkbox').val();
    if (checkUserEmail(email)) {

    } else {
        $('#delete-images-user').after('<div id="notify" class="bg-red notice marker-on-top span1"' +
                'style="z-index:10; margin:10px">' +
                'Invalid Email' +
                '</div>');
    }
}

//--------------------------------DELETE ANONYMOUS--------------------------------------//

/**
 * Checks what option is chosen and then calls for deletion when button is pressed.
 * @returns {undefined}
 */
function deleteAnonymousListeners() {                   //Initializes datepicker
    $("#delete-anonymous-date").datepicker({
        effect: "slide",
        selected: function(d, d0) {
            $('#delete-anonymous-datepicker').val(d); //Adds the selected value to the input field
        }
    });
    $('#delete-anonymous-submit').click(function() {    //Deletes anonymous on submit
        deleteAnonymous();
    })
}

/**
 * Deletes Anonymous users based on input from user
 * @returns {undefined}
 */
function deleteAnonymous() {
    var date;
    if ($('#delete-anonymous-datepicker').val() == "") {
        date = getDate($('#delete-anonymous-select').val());
        //console.log('User selection:' + $('#delete-anonymous-select').val());
    } else {
        date = $('#delete-anonymous-datepicker').val();
        //console.log("Picked date " + $('#delete-anonymous-datepicker').val());
    }

    deleteAnonymousUsers(date);
}

/**
 * Delete anonymous users based on selected date.
 * @param {type} date contains date from which user is to be deleted to.
 * @returns {undefined}
 */
function deleteAnonymousUsers(date) {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/updateAnonymousUsers.php',
        data: {'selection': date,
            'current': getCurrentDate()},
        dataType: 'json',
        success: function(data) {
            $.Notify({//notifies user about successfull email change
                content: data + " anonymous users deleted",
                style: {background: 'lime'},
            });
        },
        error: function(request, status, error) {
            alert(request.responseText);
            console.log(request.responseText);
        }
    });
}

/**
 * Converts input from dropdown to a date
 * @param {type} $age Age in text from dropdown
 * @returns {unresolved} Date in format dd.mm.yyyy
 */
function getDate($age) {
    var date;                                           //Format dd.mm.yyyy
    //console.log("inside getData()");
    switch ($age) {
        case 'Delete All' :
            date = 0;
            break;
        case 'A Week' :
            date = 1;
            break;
        case 'A Month' :
            date = 2;
            break;
        case 'Six Months' :
            date = 3
            break;
        case 'A Year' :
            date = 4;
            break;
    }

    return date;
}

/**
 *Returns current date.
 * @returns {unresolved}
 */
function getCurrentDate() {
    var currentDate = new Date().toJSON().slice(0, 10);
    return currentDate;
}

/**
 * Fills the input field with matching emails from database
 * @returns {undefined}
 */
function loadUsers() {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/getUsers.php',
        data: {},
        dataType: 'json',
        success: function(data) {
            var users = [];
            for (var i = 0; i < data.length; i++) {         //iterates all returned users
                var buffer = data[i]['email'];              //saves next email to a temporary variable,
                users.push(buffer);                         // then pushes it on the array.
                //console.log(buffer);
            }
            $("#change-access-user").autocomplete({
                source: users
            });
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log("Error");
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}


//-------------------------DELETE EXPERIMENTS---------------------------------------------------------------------------

/**
 * Deletes a chosen experiment
 * @param {type} experimentId id of chosen experiment.
 * @returns {undefined}
 */
function deleteExperiment(experimentId) {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/deleteExperiment.php',
        data: {'experimentId': experimentId},
        dataType: 'json',
        success: function(data) {
            if (data == 1) {        //that particular experiment was successfully deleted.
                $.Notify({//notifies user about successfull experiment deletion change.
                    content: "Experiment successfully deleted",
                    style: {background: 'lime'},
                });
                console.log("Deleting one experiment: " + data);
            }
        },
        error: function(request, status, error) {
            console.log("inside error");
            alert(request.responseText);
        }
    });
}

/**
 * Deletes one of more experiment based of chosen date.
 * @param {type} experimentId id of experiment
 * @param {type} date chosen date.
 * @param {type} userCheck user level.
 * @returns {undefined}
 */
function deleteExperimentOnDate(experimentId, date, userCheck) {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/updateExperiments.php',
        data: {'experimentId': experimentId,
            'userCheck': userCheck,
            'selection': date,
            'current': getCurrentDate(),
        },
        dataType: 'json',
        success: function(data) {
            if (data == 1) {        //that particular experiment was successfully deleted.
                $.Notify({//notifies user about successfull experiment deletion change.
                    content: "Experiments successfully deleted",
                    style: {background: 'lime'},
                });
                console.log("Deleting experiment on interval or date: " + data);
            }
            else {
                $.Notify({//notifies user about successfull experiment deletion change.
                    content: "Something went wrong or missing required permission",
                    style: {background: 'red'},
                });
            }
        },
        error: function(request, status, error) {
            console.log("inside error");
            alert(request.responseText);
        }

    });
}
