$(document).ready(function() {
    $('#nav-left-buttons').load("ajax/scientist/navLeft.html", function() {
        $('.dropdown-menu').dropdown();
    });
    $('#nav-right-buttons').load("ajax/scientist/navRight.html", function() {
        $('#nav-user').append(" ");
    });

    //--------------------- ADMINPANEL -----------------------------//

    $("#dashboard").click(function() {
        
    });

    //------------------------ IMAGES -------------------------------//

    $("#view-images").click(function() {

    });

    $("#upload-image").click(function() {

    });

    $("#image-sets").click(function() {

    });

    //----------------------- EXPERIMENTS -----------------------------//

    $("#view-experiments").click(function() {

    });

    $("#results").click(function() {

    });

    //------------------------- OTHER --------------------------------//

    $("#invite-scientist").click(function() {

    });
});
