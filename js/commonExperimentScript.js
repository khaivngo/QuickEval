var experimentId;

$(document).ready(function () {
    $(".fullscreen").on('click', function () {
        goFullscreen();
    });

    $(".fullscreenExit").on('click', function () {
        exitFullscreen();
    });

    $('.panning-reset').click(function () {
        $("#set1 .panzoom, #set2 .panzoom, #set3 .panzoom").panzoom("resetPan");
    });

    $('#panzoom-reset').click(function () {
        $("#set1 .panzoom, #set2 .panzoom, #set3 .panzoom").panzoom("resetPan");
    });
});

/**
 * Posts end time when experiment is finished.
 * @param {type} experimentId
 * @returns {undefined}
 */
function experimentComplete(experimentId) {
    var buffer = getDateTime();

    $.ajax({
        url: 'ajax/observer/updateExperimentResultData.php',
        async: false,
        type: 'POST',
        data: {
            "endTime": buffer,
            "experimentId": experimentId
        },
        success: function (data) {
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Error");
            console.log(xhr.status);
            console.log(thrownError);
        }
    });
}

/**
 * Posta start time and meta data when starting the experiment.
 * @param {type} experimentId
 * @returns {undefined}
 */
function postStartData(experimentId) {
    var dimension = viewport();

    $.ajax({
        url: 'ajax/observer/insertExperimentResultData.php',
        async: false,
        data: {
            'os': getOs(),
            'xDimension': dimension['width'],
            'yDimension': dimension['height'],
            'startTime': getDateTime(),
            'experimentId': experimentId
        },
        type: 'post',
        success: function (data) {},
        error: function (request, status, error) {}
    });
}

/**
 * Get's the dimensions of the viewport.
 * @returns {viewport.Anonym$0}
 */
function viewport() {
    var e = window;
    var a = 'inner';
    if (!('innerWidth' in window)) {
        a = 'client';
        e = document.documentElement || document.body;
    }
    return {width: e[a + 'Width'], height: e[a + 'Height']};
}

/**
 * Formats time values and appends zero where it is needed.
 * @param {type} number the value
 * @param {type} length length of the value
 * @returns {pad.str|String}
 */
function pad(number, length) {
    var str = '' + number;
    while (str.length < length) {
        str = '0' + str;
    }
    return str;
}

/**
 * Format times and returns value
 * @param {type} time in milliseconds
 * @returns {String} contains the whole time format
 */
function formatTime(time) {
    var min = parseInt(time / 6000),
        sec = parseInt(time / 100) - (min * 60),
        hundredths = pad(time - (sec * 100) - (min * 6000), 2);
    return (min > 0 ? pad(min, 2) : "00") + ":" + pad(sec, 2) + ":" + hundredths;
}

/**
 * Returns operating system.
 * @returns {String}
 */
function getOs() {
    var OSName = "unknown OS";
    if (navigator.appVersion.indexOf("Win") != -1)
        OSName = "Windows";
    if (navigator.appVersion.indexOf("Mac") != -1)
        OSName = "MacOS";
    if (navigator.appVersion.indexOf("X11") != -1)
        OSName = "UNIX";
    if (navigator.appVersion.indexOf("Linux") != -1)
        OSName = "Linux";

    return OSName;
}


/**
 * http://stackoverflow.com/questions/10211145/getting-current-date-and-time-in-javascript
 * @author Daniel Lee
 * @returns {String} date and time
 */
function getDateTime() {
    var now = new Date();
    var year = now.getFullYear();
    var month = now.getMonth() + 1;
    var day = now.getDate();
    var hour = now.getHours();
    var minute = now.getMinutes();
    var second = now.getSeconds();
    if (month.toString().length == 1) {
        var month = '0' + month;
    }
    if (day.toString().length == 1) {
        var day = '0' + day;
    }
    if (hour.toString().length == 1) {
        var hour = '0' + hour;
    }
    if (minute.toString().length == 1) {
        var minute = '0' + minute;
    }
    if (second.toString().length == 1) {
        var second = '0' + second;
    }
    var dateTime = year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
    return dateTime;
}

/**
 * Getting sent experiment ID from POST
 * @returns {undefined}
 */
function getExperimentIdPost() {
    $.ajax
    ({
        url: 'ajax/observer/getPostData.php',
        async: false,
        data: {},
        dataType: 'json',
        success: function (data) {
            experimentId = data;
        },
        error: function (request, status, error) {
            alert("Whoopsi!\n Something went wrong.\n\nClose this to be returned to front page.");
            window.location = 'index.php';
        }
    });
}

/**
 * Hides/show fullscreen buttons whether user are using IE.
 * http://jsfiddle.net/9zxvE/383/
 * @returns {undefined}
 */
function IESpecific() {
    var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
    // Opera 8.0+ (UA detection to detect Blink/v8-powered Opera)
    var isFirefox = typeof InstallTrigger !== 'undefined'; // Firefox 1.0+
    var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
    // At least Safari 3+: "[object HTMLElementConstructor]"
    var isChrome = !!window.chrome && !isOpera; // Chrome 1+
    var isIE = /*@cc_on!@*/false || !!document.documentMode; // At least IE6

    if (isFirefox) {
        $('.image-position').switchClass("image-position", "image-position-moz", 0);
        $('.panner-side-right').switchClass("panner-side-right", "panner-side-right-moz", 0);
    }

    if (isIE == true) {

        $('#enter-fullscreen').addClass('iefix').remove();
        $('#exit-fullscreen').addClass('iefix').remove();
        $('#ie-info-fullscreen').show();

        $('#button-finished').removeClass('button-finished');
        $('#button-finished').addClass('button-finished-IE');

        $('.image-position').each(function (i, obj) {
            $(this).removeClass('image-position');
            $(this).addClass('image-position-IE');
        });

        $('#popupButtons2').addClass('popupButton-important-IE');
        $('#rating').css("width", "65%");
        $('.panner-side-left').switchClass("panner-side-left", "panner-side-left-ie", 0);
        $('.panner-side-right').switchClass("panner-side-right", "panner-side-right-ie", 0);

    }
    else {
        $('#ie-info-fullscreen').hide();
    }
}

/**
 * Retrieves data about experiment. That includes:
 * -isPublic
 * -backgrounColour
 * -showOriginal
 * -timer
 * @param {type} experimentId
 * @returns {undefined}
 */
function getSpecificExperimentData(experimentId) {
    $.ajax
    ({
        url: 'ajax/observer/getSpecificExperimentData.php',
        async: false,
        data: {'experimentId': experimentId},
        dataType: 'json',
        type: 'post',
        success: function (data) {

            if (data[0]['isPublic'] == '1 = Public') {
            }
            else {
                //console.log("Experiment is NOT public");
            }

            if (data[0].timer == 0) {
                $('#time').hide();
            }

            if (data[0].showOriginal == 0) {
                $('#original').remove();
                $('#original-tag').remove();
                //$('#reproduction').css("margin-left", "20%");
                //$('#left-reproduction').css("margin-left", "20%");
                //$('#drop-left').css("margin-left", "23.5%");
                $('#drop-right').css("margin-left", "3%");
                //$('#button-finished').css("margin-top", "30%");
            }

            if (data[0].backgroundColour != null)
                setBackgroundColour(data[0].backgroundColour);

        },
        error: function (request, status, error) {

        }
    });
}

/*------------------------------- http://code-tricks.com/fullscreen-browser-window-with-jquery/ --------------------------------------*/

/**
 * Enables fullscreens mode
 * @returns {undefined}
 */
function goFullscreen() {
    var docElement, request;
    docElement = document.documentElement;
    request = docElement.requestFullScreen || docElement.webkitRequestFullScreen || docElement.mozRequestFullScreen || docElement.msRequestFullScreen;
    if (typeof request != "undefined" && request) {
        request.call(docElement);
    }
}

/**
 * Exits fullscreen mode
 * @returns {undefined}
 */
function exitFullscreen() {
    var docElement, request;
    docElement = document;
    request = docElement.cancelFullScreen || docElement.webkitCancelFullScreen || docElement.mozCancelFullScreen || docElement.msCancelFullScreen || docElement.exitFullscreen;
    if (typeof request != "undefined" && request) {
        request.call(docElement);
    }
}

/*-------------------------------------------------------------------------------------------------------------------------------------------*/


/**
 * Changes background colour for the experiment.
 * @param {type} colour
 * @returns {undefined}
 */
function setBackgroundColour(colour) {
    $('body').css('background-color', '' + colour + '!important');
    $('#rating').css('background-color', '' + colorLuminance(colour, -0.2) + '!important');

}

/**
 * Checks if user has taken the experiment earlier.
 * @returns {undefined}
 */
function checkIfExperimentTaken() {
    $.ajax
    ({
        url: 'ajax/observer/getShowTimer.php',
        async: false,
        data: {'experimentId': experimentId},
        type: 'post',
        dataType: 'json',
        success: function (data) {

        },
        error: function (request, status, error) {

        }
    });
}


/**
 * Disables panning.
 */
function disablePanning() {
   // automaticPanningReset();
    //console.log("Disable panning");
    var elem;

    //elem = $('#pan1, #pan2, #pan3');
    //elem.panzoom("disable");

    //$('.panning-reset').remove();
    $('#drop-left').css("height", "40%");
    $('#drop-left').css("width", "30%");

    $('#drop-right').css("height", "40%");
    $('#drop-right').css("width", "30%");

    $('#rating-container').css("margin-top", "5%");
}

var panningControl = false;
function automaticPanningReset() {

    if (panningControl == true) {
        //Automatic panning reset
        $('#drop-left .panzoom, #drop-right .panzoom, #original .panzoom').mousedown(function () {
            //console.log('Mousedown');
            $('body').mouseup(function () {
                //console.log('Mouseup');
                $("#set1 .panzoom, #set2 .panzoom, #set3 .panzoom").panzoom("resetPan");
            });

            $('body').mouseleave(function () {

                // Create a new mouse up object with 'which' specified to be 1.
                var e = $.Event("mouseup", {which: 1});
                // Triggers it on the body.
                $("body").trigger(e);
                //console.log("leave")
            });

        });
        //
    }
}

function adjustScaling() {
    var $elem;

    $elem = $('#pan1, #pan2, #pan3, #pan4');

    $elem.panzoom("option", {
        increment: 0.4,
        minScale: 0.1,
        maxScale: 6,
        duration: 500,
        $reset: $("a.reset-panzoom, button.reset-panzoom")
    });

}

//

/**
 * Checks dimensions of picture, and if picture is small enough to disable panning
 * @param originalUrl location/adress of the original picture.
 */
function panningCheck(originalUrl) {
    var img = new Image();

    img.onload = function () {
        //console.log("Image dimensions: " + this.width + 'x' + this.height);
        if (this.width < 500 && this.height < 500) {
            panningControl = true;

            disablePanning();
            return;
        }
        else if (this.width < 500 || this.height < 450) {
            panningControl = true;

            disablePanning();
            return;
        }

        console.log("false")
        panningControl = false;

    };
    img.src = originalUrl;
}


/**
 *Returns either a shade darker or lighter than input. Does not work if the color is entirely black
 * eg. #000, #000000.
 *
 * @param hex — a hex color value such as “#abc” or “#123456″ (the hash is optional)
 * @param lum — the luminosity factor, i.e. -0.1 is 10% darker, 0.2 is 20% lighter, etc.
 * @returns {string} the color that is either lighter or darker
 * @constructor
 *
 * @author {http://www.sitepoint.com/javascript-generate-lighter-darker-color/}
 */
function colorLuminance(hex, lum) {
    if (hex != '') {
        // validate hex string
        hex = String(hex).replace(/[^0-9a-f]/gi, '');
        if (hex.length < 6) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        lum = lum || 0;

        // convert to decimal and change luminosity
        var rgb = "#", c, i;
        for (i = 0; i < 3; i++) {
            c = parseInt(hex.substr(i * 2, 2), 16);
            c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
            rgb += ("00" + c).substr(c.length);
        }

        return rgb;
    }
}

/**
 * Deletes all results for a user for a chosen experiment.
 */
function deleteOldResults(experimentId) {
    $.ajax({
        url: 'ajax/observer/deleteOldResults.php',
        data: {
            'experimentId': experimentId,
            'check': 0
        },
        type: 'post',
        dataType: 'json',
        async: false,
        success: function (data) {
            //console.log("Rows deleted = "+data);
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });

}
