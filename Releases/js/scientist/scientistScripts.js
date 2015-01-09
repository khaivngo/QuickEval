$(document).ready(function() {


    inject("ajax/scientist/dashboard.html"); 			//Starts on dashboard-page on refresh

    setUpModeMenu('.scientist-mode');



    //--------------------- ADMINPANEL -----------------------------//

    $("#dashboard").click(function() {
        inject("ajax/scientist/dashboard.html");
        setActive($(this));
        $(document.body).off('click', '.tile');
    });

    //------------------------ IMAGES -------------------------------//

    $("#image-sets").click(function() {
        inject("ajax/scientist/editImageSets.html");
        setActive($(this));
    });

    //----------------------- EXPERIMENTS -----------------------------//

    $("#view-experiments").click(function() {
        inject("ajax/scientist/viewExperiments.html");
        setActive($(this));
    });

    $("#results").click(function() {
        inject("ajax/scientist/experimentResults.html");
        setActive($(this));
    });

    //------------------------- OTHER --------------------------------//

    $("#invite-scientist").click(function() {
        inject("ajax/scientist/inviteScientist.html");
        setActive($(this));
    });

    //------------------------- OTHER --------------------------------//

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
            $('#right-menu').empty();
        },
        error: function(request, status, error) {
            alert(request.responseText);
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




