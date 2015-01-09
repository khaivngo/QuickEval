//Scripts used between the main components

(function($) {
    
    /**
     * Moves viewport to display selected object
     * @returns {_L3.$.fn}
     */
    $.fn.goTo = function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top + 'px'
        }, 'fast');
        return this;
    }
})(jQuery);


/**
 * Returns the database-tuple of the current user from the session if no parameter is set.
 * If a parameter is set, the function will return the value of the parameter from the tuple.
 * @param {type} $var The value to be returned from the session
 * @returns {data|Array} The value of the parameter, or full array of the tuple if no parameter is set
 */
function getUserSession($var) {
    var userData = new Array();
    $.ajax
            ({
                url: 'ajax/observer/getUserData.php',
                async: false,
                dataType: 'json',
                success: function(data) {
                    userData = data;

                },
                error: function(request, status, error) {
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
    $.ajax
            ({
                url: 'ajax/scientist/getImagesetData.php',
                async: false,
                type: 'POST',
                data: {
                    'option': "getUploadedPictures",
                    'imagesetId': imagesetId
                },
                dataType: 'json',
                success: function(data) {
                    console.log("Got all images in set! = " + data);	//FJERN
                    set = data;
                },
                error: function(request, status, error) {
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

    if (type == 1) {    //Admin
        $('#nav-left-buttons').load("ajax/admin/navLeft.html", function() {
            $('.dropdown-menu').dropdown();
            $('.modes').removeClass('bg-darkCyan');
            $($target).addClass('bg-darkCyan');
            setUpModeMenuListeners();
        });
        $('#nav-right-buttons').load("ajax/admin/navRight.html", function() {
            $('#nav-user').append(" " + getUsername());
            setUpLogout();
        });

    } else if (type == 3) {  //Observer or Anonymous
        $('#nav-left-buttons').load("ajax/observer/navLeft.html", function() {
            $('.dropdown-menu').dropdown();
            setUpModeMenuListeners(type);
        });
        $('#nav-right-buttons').load("ajax/observer/navRight.html", function() {
            $('#nav-user').append(" " + getUsername());
            setUserIcon();
            setUpLogout();
        });
    } else if (type == 4) {
        $('#nav-left-buttons').load("ajax/observer/navLeft.html", function() {
            $('.dropdown-menu').dropdown();
            setUpModeMenuListeners(type);
        });
        $('#nav-right-buttons').load("ajax/observer/navRightAnon.html", function() {
            $('#nav-user').append(" " + getUsername());
            setUserIcon();
            setUpLogout();
        });
    } else if (type == 2) { //Scientist
        $('#nav-left-buttons').load("ajax/scientist/navLeft.html", function() {
            $('.dropdown-menu').dropdown();
            $('.modes').removeClass('bg-darkCyan');
            $($target).addClass('bg-darkCyan');
            setUpModeMenuListeners();
        });
        $('#nav-right-buttons').load("ajax/scientist/navRight.html", function() {
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
    $('.observer-mode').click(function() {
        window.location = "index.php";
    });

    $('.scientist-mode').click(function() {
        window.location = "scientistpanel.php";
    });

    $('.admin-mode').click(function() {
        window.location = "adminpanel.php";
    });
}

/**
 * Adds listener to logout button
 * @returns {undefined}
 */
function setUpLogout() {
    $('#logout').click(function() {
        window.location = "logout.php";
    });
}



