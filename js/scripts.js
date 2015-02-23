//Scripts used between the main components

(function ($) {

    /**
     * Moves viewport to display selected object
     * @returns {_L3.$.fn}
     */
    $.fn.goTo = function () {
        $('html, body').animate({
            scrollTop: $(this).offset().top + 'px'
        }, 'fast');
        return this;
    }
})(jQuery);

//Defines a delay
var delay = (function () {
    var timer = 0;
    return function (callback, ms) {
        clearTimeout(timer);
        timer = setTimeout(callback, ms);
    };
})();

/**
 * Redirects to login
 */
function login() {
    window.location = 'login.php';
}

/**
 * Redirects to register
 */
function register() {
    window.location = 'register.php';
}

/**
 * Gets available experiment types from database.
 * @returns {result} array with experimenttypes
 */
function loadExperimentTypes() {
    var result = new Array();
    $.ajax({
        async: false,
        url: 'ajax/scientist/getExperimentTypes.php',
        dataType: 'json',
        success: function (data) {
            result = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
            console.log(request.responseText);
            result = 0;
        }
    });
    return result;
}

/**
 * Returns the database-tuple of the current user from the session if no parameter is set.
 * If a parameter is set, the function will return the value of the parameter from the tuple.
 * @param {type} $var The value to be returned from the session
 * @returns {data|Array} The value of the parameter, or full array of the tuple if no parameter is set
 */
function getUserSession($var) {
    var userData = new Array();
    $.ajax({
        url: 'ajax/observer/getUserData.php',
        url: 'ajax/observer/getUserData.php',
        async: false,
        dataType: 'json',
        success: function (data) {
            userData = data;

        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    if (typeof $var === 'undefined') {
        return userData;
    }
    return userData[$var];
}

/**
 * Returns the full name of the logged in user, or only first name if the user is anonymous.
 * @returns {String} The full name of the logged in user
 */
function getUsername() {

    var userData = new Array();
    userData = getUserSession();

    return userData['firstName'] + " " + ((userData['lastName'] != null) ? userData['lastName'] : "");
}

/**
 * Sets the right icon for the user based on user-type. Normal icon for users, icon in suits for admin/scientist
 * @returns {undefined}
 */
function setUserIcon() {
    var userData = new Array();
    userData = getUserSession();

    if (userData['userType'] == 1 || userData['userType'] == 2) {
        $('#user-icon').addClass('icon-user-3');
    } else {
        $('#user-icon').addClass('icon-user');
    }
}

/**
 * Gets all images in a imageset
 * @param {type} imagesetId ID for the selected image set
 * @returns {data|Array} Array with images
 */
function getAllImagesInSet(imagesetId) {
    var set = new Array();
    $.ajax({
        url: 'ajax/scientist/getImagesetData.php',
        async: false,
        type: 'POST',
        data: {
            'option': "getUploadedPictures",
            'imagesetId': imagesetId
        },
        dataType: 'json',
        success: function (data) {
            console.log("Got all images in set! = " + data); //FJERN
            set = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    return set;
}

/**
 * Sets up user menu's based on usertype
 * @param {type} $target Which menu should be targeted, i.e. '.observer-mode' or '.admin-mode'
 * @returns {undefined}
 */
function setUpModeMenu($target) {
    var type = getUserSession('userType');

    if (type < 2) { //Admin
        $('#nav-left-buttons').load("ajax/admin/navLeft.html", function () {
            $('.dropdown-menu').dropdown();
            $('.modes').removeClass('bg-darkCyan');
            $($target).addClass('bg-darkCyan');
            setUpModeMenuListeners();
        });
        $('#nav-right-buttons').load("ajax/admin/navRight.html", function () {
            $('#nav-user').append(" " + getUsername());
            setUpLogout();
        });

    } else if (type == 3) { //Observer 
        $('#nav-left-buttons').load("ajax/observer/navLeft.html", function () {
            $('.dropdown-menu').dropdown();
            setUpModeMenuListeners(type);
        });
        $('#nav-right-buttons').load("ajax/observer/navRight.html", function () {
            $('#nav-user').append(" " + getUsername());
            setUserIcon();
            setUpLogout();
        });
    } else if (type == 4) { //Anonymous
        $('#nav-left-buttons').load("ajax/observer/navLeft.html", function () {
            $('.dropdown-menu').dropdown();
            setUpModeMenuListeners(type);
        });
        $('#nav-right-buttons').load("ajax/observer/navRightAnon.html", function () {
            $('#nav-user').append(" " + getUsername());
            setUserIcon();
            setUpLogout();
        });
    } else if (type == 2) { //Scientist
        $('#nav-left-buttons').load("ajax/scientist/navLeft.html", function () {
            $('.dropdown-menu').dropdown();
            $('.modes').removeClass('bg-darkCyan');
            $($target).addClass('bg-darkCyan');
            setUpModeMenuListeners();
        });
        $('#nav-right-buttons').load("ajax/scientist/navRight.html", function () {
            $('#nav-user').append(" " + getUsername());
            setUpLogout();
        });
    }
}

/**
 * Sets up listeners for "modes", index, scientist- and adminpanel
 * @param {type} $anonymous
 * @returns {undefined}
 */
function setUpModeMenuListeners($anonymous) {
    $('.observer-mode').click(function () {
        window.location = "index.php";
    });

    $('.scientist-mode').click(function () {
        window.location = "scientistpanel.php";
    });

    $('.admin-mode').click(function () {
        window.location = "adminpanel.php";
    });

}

/**
 * Adds listener to logout button
 * @returns {undefined}
 */
function setUpLogout() {
    $('#logout').click(function () {
        window.location = "logout.php";
    });
}

//-------DELETE INSTRUCTION--------------------------------------------

/**
 * Click listenernes and preparation of area.
 */
function setupClickListenerDeleteInstruction(mode) {

    getInstructionForDeletion(1, mode);
    $("#delete-instruction-dialog").hide();
    submitButtonCheck();


    $('#select-right-delete-instruction').click(function () {
        var selectedArray = $("#sel1").val();
        addOptionForDeletion(selectedArray);
    });

    $('#select-left-delete-instruction').click(function () {
        var selectedArray = $("#sel2").val();
        removeOptionForDeletion(selectedArray)
    });


    $('#select-all-delete-instruction').click(function () {
        moveAllOptions();
    });

    $('#select-reset-delete-instruction').click(function () {
        resetOptions();
    });

    $('#submit-delete-instruction').click(function () {
        $("#delete-instruction-dialog").show();

        $('#confirm-delete-instruction').click(function () {
            $("#delete-instruction-dialog").hide();
            deleteInstructions();
        });

        $('#cancel-delete-instruction').click(function () {
            $("#delete-instruction-dialog").hide();
        });

    });
}

/**
 * Adds selected options to deletion list.
 * @param array contains value of each selected element.
 */
function addOptionForDeletion(array) {

    array.forEach(function (selected) {
        var text = $('#sel1 option[value="' + selected + '"]').html();
        var value = $('#sel1 option[value="' + selected + '"]').val();


        //Only instructions that are not in use.
        if (value != "!") {
            $("#sel2").append($('<option></option>').val(selected).html(text));
            $("#sel1 option[value='" + selected + "']").remove();
        }
    });

    $('#sel2 option[selected="selected"]').each(
        function () {
            $(this).removeAttr('selected');
        }
    );

    $("#sel1 option:first").attr('selected', 'selected');

    submitButtonCheck();
}

/**
 * Removes selected instruction away from deletion
 * @param array containing value of selected instructions
 */
function removeOptionForDeletion(array) {
    array.forEach(function (selected) {
        var text = $('#sel2 option[value="' + selected + '"]').html();
        $("#sel1").append($('<option></option>').val(selected).html(text));

        $("#sel2 option[value='" + selected + "']").remove();
    });

    $('#sel1 option[selected="selected"]').each(
        function () {
            $(this).removeAttr('selected');
        }
    );

    $("#sel2 option:first").attr('selected', 'selected');
    submitButtonCheck();
}

/**
 * Moves all instructions for deletion
 */
function moveAllOptions() {
    var value;
    var text;
    $("#sel1 option").each(function (index) {
        value = $(this).val();
        text = $(this).text();

        $("#sel2").append($('<option></option>').val(value).html(text));

        $("#sel1 option[value='" + value + "']").remove();
    });

    submitButtonCheck();
}

var instructionArray = [];
/**
 * Resets all instruction that have been moved to deletion
 */
function resetOptions() {
    var counter = 0;
    //Empties both list.
    $("#sel1").find('option').remove().end();
    $("#sel2").find('option').remove().end();

    //Goes through an two-dimensional array and readds instruction to list.
    instructionArray.forEach(function (index) {
        $("#sel1").append($('<option></option>').val(index.value).html(index.text));
    });

    instructionsInUse(2);
    submitButtonCheck();

}

/**
 * Retrieves all instructions associated with user.
 * @param option determines how the script is runned.
 */
function getInstructionForDeletion(option, mode) {
    var i = 0;
    console.log("mode: "+mode);
    $.ajax({
        url: 'ajax/admin/getInstructionsFromHistory.php',
        async: false,
        type: 'POST',
        data: {
            'option': option,
            'mode'  : mode
        },
        dataType: 'json',
        success: function (data) {
            console.log(data);
            data.forEach(function (instruction) {
                instructionArray.push({text: instruction.text, title: instruction.title, id: instruction.id});
                $("#sel1").append($('<option></option>').val(instruction.id).html(instruction.text));
                i++;
            });
            console.log(i);
            instructionsInUse(1, mode);
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}

var arrayInstructionsInUse = [];
/**
 * Fetches all instructions that belongs to current user and is associated with an experiment.
 * @param option whether to fetch new data or update.
 */
function instructionsInUse(option, mode) {
    if (option == 1) {
        $.ajax({
            url: 'ajax/admin/getInstructionsFromHistory.php',
            async: false,
            type: 'POST',
            data: {
                'option': 3,
                'mode': mode
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                data.forEach(function (instruction) {
                    //saves returned data in an array of objects.
                    arrayInstructionsInUse.push({text: instruction.text, title: instruction.title, id: instruction.id});
                    $('#sel1 option').filter(function () {
                        return $(this).html() == instruction.text;
                    }).html(instruction.text + ' [ ' + instruction.title + ' ]').val("!"); //uses ! to mark option not deletable.
                });
            },
            error: function (request, status, error) {
                console.log(request.responseText);
            }
        });
    }
    else if (option == 2) {
        arrayInstructionsInUse.forEach(function (instruction) {
            $('#sel1 option').filter(function () {
                return $(this).html() == instruction.text;
            }).html(instruction.text + ' [ ' + instruction.title + ' ]').val("!"); //uses ! to mark option not deletable.
        });
    }
}

/**
 * Goes through all selected options and saves in an array
 * which is sent to PHP for deletion.
 */
function deleteInstructions() {
    var instructions = [];
    var stringInstructions;

    $("#sel2 option").each(function () {
        instructions.push($(this).val());
    });

    $.ajax({
        type: 'POST',
        url: 'ajax/admin/updateInstructions.php',
        data: {'selection': JSON.stringify(instructions)},
        success: function (data) {
            $.Notify({//notifies user about successfull deletion of instructions
                content: "Instruction(s) deleted",
                style: {background: 'lime'}
            });
        },
        error: function (request, status, error) {
        }
    });

    $("#sel2").find('option').remove().end();   //clear list.
}

/**
 * Checks whether the submit buttonn is to be disabled or not.
 */
function submitButtonCheck() {
    if ($('#sel2 option').length == 0) {
        $("#submit-delete-instruction").prop("disabled", true);
    }
    else {
        $("#submit-delete-instruction").prop("disabled", false);

    }
}