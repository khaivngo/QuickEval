$(document).ready(function() {


    inject("ajax/admin/dashboard.html"); 			//Starts on dashboard-page on refresh

    setUpModeMenu('.admin-mode');

    //--------------------- ADMINPANEL -----------------------------//

    $("#dashboard").click(function() {
        inject("ajax/admin/dashboard.html")
        setActive($(this));
    });

    //------------------------ IMAGES -------------------------------//

    $("#delete-images").click(function() {
        inject("ajax/admin/deleteImages.html")
        setActive($(this));
        deleteImagesListeners();
    });

    //------------------------ USERS -------------------------------//

    $("#register-scientist").click(function() {
        inject("ajax/admin/registerScientist.html")
        setActive($(this));
    });

    $("#delete-anonymous").click(function() {
        inject("ajax/admin/deleteAnonymous.html")
        setActive($(this));
        
        deleteAnonymousListeners();
    });


    $("#change-access").click(function() {
        inject("ajax/admin/changeAccess.html")
        setActive($(this));
        userAccessListeners();
        getAccessNames();
    });

    //----------------------- EXPERIMENTS -----------------------------//

    $("#delete-experiments").click(function() {
        inject("ajax/admin/deleteExperiments.html")
        setActive($(this));
    });
});

/**
 * Inects a html-page into the right panel div.
 * $data is location of html file.
 */
function inject($data) {

    $.ajax({
        async: false,
        url: $data,
        success: function(data) {
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

function getUsername() {
    var username = "Christopher Dokkeberg";
    return username;
}