/***************************/
//@Author: Adrian "yEnS" Mato Gondelle
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!
        /***************************/

//SETTING UP OUR POPUP
//0 means disabled; 1 means enabled;
        var popupStatus = 0;
var popupStatus2 = 0;
var popupStatus3 = 0;
var popupStatus4 = 0;
window.allowClickOut = 0;

//loading popup with jQuery magic!
function loadPopup() {
    //loads popup only if it is disabled
    if (popupStatus == 0) {
        $("#backgroundPopup").css({
            "opacity": "0.7"
        });
        $("#backgroundPopup").fadeIn("fast");
        $("#popupContact").fadeIn("fast");
        popupStatus = 1;
    }
}

//-------------------------------------------------------------------------

//loading popup with jQuery magic!
function loadPopup2() {
    //loads popup only if it is disabled
    if (popupStatus2 == 0) {
        $("#backgroundPopup2").css({
            "opacity": "0.7"
        });
        $("#backgroundPopup2").fadeIn("fast");
        $("#popupContact2").fadeIn("fast");
        popupStatus2 = 1;
    }
}

//-------------------------------------------------------------------------

//loading popup with jQuery magic!
function loadPopup3() {

    //loads popup only if it is disabled
    if (popupStatus3 == 0) {
        $("#backgroundPopup3").css({
            "opacity": "0.7"
        });
        $("#backgroundPopup3").fadeIn("fast");
        $("#popupContact3").fadeIn("fast");
        popupStatus3 = 1;
    }
}


//-------------------------------------------------------------------------

//loading popup with jQuery magic!
function loadPopup4() {

    //loads popup only if it is disabled
    if (popupStatus3 == 0) {
        $("#backgroundPopup4").css({
            "opacity": "0.7"
        });
        $("#backgroundPopup4").fadeIn("fast");
        $("#popupContact4").fadeIn("fast");
        popupStatus4 = 1;
    }
}

//----------------------------------------------------------------------------------------------------------------------------------------------------


//disabling popup with jQuery magic!
function disablePopup() {
    //disables popup only if it is enabled
    if (popupStatus == 1) {
        $("#backgroundPopup").fadeOut("fast");
        $("#popupContact").fadeOut("fast");
        popupStatus = 0;
    }
}

//disabling popup with jQuery magic!
function disablePopup2() {
    //disables popup only if it is enabled
    if (popupStatus2 == 1) {
        $("#backgroundPopup2").fadeOut("fast");
        $("#popupContact2").fadeOut("fast");
        popupStatus2 = 0;
    }
}

//-------------------------------------------------------------------------

//disabling popup with jQuery magic!
function disablePopup3() {
    //disables popup only if it is enabled
    if (popupStatus3 == 1) {
        $("#backgroundPopup3").fadeOut("fast");
        $("#popupContact3").fadeOut("fast");
        popupStatus3 = 0;
    }
}

//-------------------------------------------------------------------------

//disabling popup with jQuery magic!
function disablePopup4() {
    //disables popup only if it is enabled
    if (popupStatus4 == 1) {
        $("#backgroundPopup4").fadeOut("fast");
        $("#popupContact4").fadeOut("fast");
        popupStatus4 = 0;
    }
}

//----------------------------------------------------------------------------------------------------------------------------------------------------

//centering popup
function centerPopup() {
    //request data for centering
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $("#popupContact").height();
    var popupWidth = $("#popupContact").width();
    //centering
    $("#popupContact").css({
        "position": "absolute",
        "top": windowHeight / 2 - popupHeight / 2,
        // "left": windowWidth / 2 - popupWidth / 2          //removed extra margin
    });
    //only need force for IE6

    $("#backgroundPopup").css({
        "height": windowHeight
    });

}

//-------------------------------------------------------------------------

//centering popup
function centerPopup2() {
    //request data for centering
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $("#popupContact2").height();
    var popupWidth = $("#popupContact2").width();
    //centering
    $("#popupContact2").css({
        "position": "absolute",
        "top": windowHeight / 2 - popupHeight / 2,
        //"left": windowWidth / 2 - popupWidth / 2      //removed extra margin
    });
    //only need force for IE6

    $("#backgroundPopup2").css({
        "height": windowHeight
    });

}


//-------------------------------------------------------------------------

//centering popup
function centerPopup3() {

    //request data for centering
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $("#popupContact3").height();
    var popupWidth = $("#popupContact3").width();
    //centering
    $("#popupContact3").css({
        "position": "absolute",
        "top": windowHeight / 2 - popupHeight / 2,
        //"left": windowWidth / 2 - popupWidth / 2      //removed extra margin
    });
    //only need force for IE6

    $("#backgroundPopup3").css({
        "height": windowHeight
    });

}


//-------------------------------------------------------------------------

//centering popup
function centerPopup4() {

    //request data for centering
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.documentElement.clientHeight;
    var popupHeight = $("#popupContact4").height();
    var popupWidth = $("#popupContact4").width();
    //centering
    $("#popupContact4").css({
        "position": "absolute",
        "top": windowHeight / 2 - popupHeight / 2,
        //"left": windowWidth / 2 - popupWidth / 2      //removed extra margin
    });
    //only need force for IE6

    $("#backgroundPopup4").css({
        "height": windowHeight
    });

}

//----------------------------------------------------------------------------------------------------------------------------------------------------

//CONTROLLING EVENTS IN jQuery
$(document).ready(function() {

    //LOADING POPUP
    //Click the button event!
    $("#cancel-experiment").click(function() {
        //centering with css
        centerPopup();
        //load popup
        loadPopup();
    });

    //CLOSING POPUP
    //Click the x event!
    $("#popupContactClose").click(function() {
        disablePopup();
    });

    $("#continue").click(function() {       //Added
        disablePopup();
    });

    $("#continue2").click(function() {       //Added
        disablePopup2();
    });

    //Click out event!
    $("#backgroundPopup").click(function() {
        disablePopup();
    });

    //Press Escape event!

    $(document).keydown(function(e) {       //Modified from keypress to keydown
        if (e.keyCode == 27 && popupStatus == 1) {
            disablePopup();
        }
    });


//------------------------------------------------------------------------------------------------------

    //LOADING POPUP
    //Click the button event!
    $("#button-instruction").click(function() {
        //centering with css
        centerPopup2();
        //load popup
        loadPopup2();
    });


    //CLOSING POPUP
    //Click the x event!
    $("#popupContactClose2").click(function() {
        disablePopup2();
    });

    $("#continue2").click(function() {       //Added
        disablePopup2();
    });

//    //Click out event!
//    $("#backgroundPopup2").click(function() {
//        disablePopup2();
//    });

    //Press Escape event!
    $(document).keydown(function(e) {       //Modified from keypress to keydown
        if (e.keyCode == 27 && popupStatus == 1) {
            disablePopup2();
        }
    });


    //--------------------------Quit popup----------------------------------------------------------------------------

    //CLOSING POPUP
    //Click the x event!
    $("#popupContactClose3").click(function() {
        disablePopup3();
    });

    $("#continue3").click(function() {       //Added
        disablePopup3();
    });

//    //Click out event!
//    $("#backgroundPopup3").click(function() {
//        disablePopup3();
//    });

//    //Press Escape event!
//    $(document).keydown(function(e) {       //Modified from keypress to keydown
//        if (e.keyCode == 27 && popupStatus == 1) {
//            disablePopup3();
//        }
//    });



    //--------------------------Rating next popup----------------------------------------------------------------------------

    //CLOSING POPUP
    //Click the x event!
    $("#popupContactClose4").click(function() {
        disablePopup4();
    });

    $("#continue4, #button-next-rating").click(function() {       //Added
        disablePopup4();
    });

//    //Click out event!
//    $("#backgroundPopup3").click(function() {
//        disablePopup3();
//    });

    //Press Escape event!
    $(document).keydown(function(e) {       //Modified from keypress to keydown
        if (e.keyCode == 27 && popupStatus4 == 1) {
            disablePopup4();
        }
    });




});
