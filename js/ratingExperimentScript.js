var type;
var dragStarted = false;
var dropRightCheck = false;
var dropLeftCheck = false;
var isDown = false;   // Tracks status of mouse button
var draggingUrl = "";
var draggingPosition;

$(document).ready(function () {


    //var container = $('body');
    //$('#rating-images').sortable({
    //    containment: container,
    //    helper: 'clone', //clones draggable and appends to body so that it appears to be infront even with overflow:scroll
    //    appendTo: 'body',
    //    zIndex: 105, //or greater than any other relative/absolute/fixed elements and droppables
    //    scroll: true,
    //    handle: $('#rating-images'),
    //    stop: function (event, ui) {
    //        var position = ui.item.index() + 1; //getting new position of element
    //        var id = ui.item[0].id; //getting id of touched element
    //        $("#rating-images #" + id + "").addClass('touched'); //passing id of element to be marked as visited.
    //
    //        updateSortablePosition();
    //    }
    //});
    // $('#rating-images').disableSelection();


    // handle + event
    var container = document.getElementById("rating-images");
    if (container) {

        new Sortable(container, {
            animation: 150, // ms, animation speed moving items when sorting, `0` � without animation
            handle: ".tile-sortable", // css-selector, which can be used to drag
            draggable: ".tile-sortable", // css-selector of elements, which can be sorted
            onUpdate: function (/**Event*/evt) {
                var item = evt.item; // a link to an element that was moved

                // console.log(item.id)
                $("#rating-images #" + item.id + "").addClass('touched'); //passing id of element to be marked as visited.

                updateSortablePosition();

                // console.log("sortable handler")
            },
            // Element dragging ended
            // onEnd: function (/**Event*/evt) {
            // onStart: function (/**Event*/evt) {
            //     evt.oldIndex;  // element's old index within parent
            //     evt.newIndex;  // element's new index within parent
            //     dragStarted = true;
            //     // console.log(evt)
            //
            //     // console.log(evt.item.children[1].attributes[0].value)
            //
            //     var imgUrl = evt.item.children[1].attributes[0].value;
            //
            //     if ($('#drop-right').is(':hover')) {
            //         console.log("hovering drop right")
            //     }
            //
            //
            // },
            // onEnd: function (/**Event*/evt) {
            //     dragStarted = false;
            //     console.log("Drag ended")
            //
            //     var draggableUrl = evt.item.children[1].attributes[0].value;
            //     var draggableId = evt.item.children[3].textContent;
            //
            //     console.log(dropRightCheck, dropLeftCheck, isDown)
            //
            //     if (dropRightCheck && !dropLeftCheck && isDown) {
            //         if (draggingUrl != "") {
            //             console.log("swapping right")
            //             $('#drop-right').find('img').attr('src', draggableUrl);
            //             $('#drop-right').find('img').remove();
            //             $('#pan2').prepend('<img class= "picture" src=' + draggableUrl + ' pictureOrderId = ' + draggableId + ' />');
            //             pictureInPanner(draggableId, "right");
            //         }
            //     }
            //     else if (!dropRightCheck && dropLeftCheck) {
            //
            //     }
            //
            // },

        });

    }


    $(document).mousedown(function (event) {
        isDown = true;      // When mo// use goes down, set isDown to true
        // console.log("mouse down")
        // console.log($(event.target).closest('.image-position').find('#initial-position').text())

        draggingPosition = $(event.target).closest('.image-position').find('#initial-position').text();

        // console.log($(event.target).parent().find(".initial-position").text())
        draggingUrl = event.target.src

    }).mouseup(function () {
        setTimeout(function () {
            isDown = false;    // When mouse goes up, set isDown to false
            draggingUrl = "";


        }, 1000);


    });


    $("#drop-right").mouseover(function () {
        // console.log("entering drop-right")
        dropRightCheck = true;

        if (dropRightCheck && !dropLeftCheck && isDown) {
            if (draggingUrl != "" && draggingPosition != "") {
                // var draggableId = "654654";

                console.log("swapping right")
                $('#drop-right').find('img').attr('src', draggingUrl);
                $('#drop-right').find('img').remove();
                $('#pan2').prepend('<img class= "picture" src=' + draggingUrl + ' pictureOrderId = ' + draggingPosition + ' />');
                pictureInPanner(draggingPosition, "right");

                draggingPosition = "";
                draggingUrl = ""
            }
        }
        // else if (!dropRightCheck && dropLeftCheck) {
        //     if (draggingUrl != "" && draggingPosition != "") {
        //         // var draggableId = "654654";
        //
        //         console.log("swapping left")
        //         $('#drop-left').find('img').attr('src', draggingUrl);
        //         $('#drop-left').find('img').remove();
        //         $('#pan1').prepend('<img class= "picture" src=' + draggingUrl + ' pictureOrderId = ' + draggingPosition + ' />');
        //         pictureInPanner(draggingPosition, "right");
        //
        //         draggingPosition = "";
        //         draggingUrl = ""
        //     }
        //
        // }

    });

    $("#drop-right").mouseleave(function () {
        // console.log("leave drop-right")
        dropRightCheck = false
    });


    $("#drop-left").mouseover(function () {
        // console.log("entering drop-left")
        dropLeftCheck = true;
        if (!dropRightCheck && dropLeftCheck) {
            if (draggingUrl && draggingUrl != "" && draggingPosition != "") {
                // var draggableId = "654654";

                console.log("swapping left")
                $('#drop-left').find('img').attr('src', draggingUrl);
                $('#drop-left').find('img').remove();
                $('#pan1').prepend('<img class= "picture" src=' + draggingUrl + ' pictureOrderId = ' + draggingPosition + ' />');
                pictureInPanner(draggingPosition, "left");

                draggingPosition = "";
                draggingUrl = ""
            }

        }

    });

    $("#drop-left").mouseleave(function () {
        // console.log("leave drop-left")
        dropLeftCheck = false;
    });

    //----------------------------------------------------------------------------------------------------------------------------------------------------

    (function () {
        var $section = $('#set1, #set2, #set3');
        $section.find('.panzoom').panzoom({
            $zoomIn: $section.find(".zoom-in"),
            $zoomOut: $section.find(".zoom-out"),
            $zoomRange: $section.find(".zoom-range"),
            $reset: $section.find(".reset"),
            $set: $section.find('.parent > div'),
            // contain: 'invert',
            minScale: 1,
            // maxScale: 1.30
            maxScale: 1
        }).panzoom('zoom');
    })();

//----------------------------------------------------------------------------------------------------------------------------------------------------  

    //left drag panner/ drop area
    $(function () {
        // $(".image-position").draggable({
        //     helper: 'clone'
        // });
        $("#drop-left .parent").droppable({
            drop: function (event, ui) {
                var draggableId = ui.draggable.find('img').attr("id");
                var aId = ui.draggable.find('a').attr("id");
                var droppableId = $(this).attr("id");
                var draggableUrl = ui.draggable.find('img').attr("src");
                var fetchedInitialPosition = ui.draggable.find('#initial-position').text();

                console.log(draggableUrl);

                $('#drop-left').find('img').attr('src', draggableUrl);
                $('#drop-left').find('img').remove();
                $('#pan1').prepend('<img class= "picture" src=' + draggableUrl + ' pictureOrderId = ' + draggableId + ' />');

                pictureInPanner(fetchedInitialPosition, "left");
            }
        });
    });

    //right drag panner/drop area
    $(function () {
        $(".image-position").draggable({

            helper: 'clone',
            revert: 'invalid',
            appendTo: 'body',
            // drag: function() {
            //    console.log(this)
            // },
        });

        $('.image-position').draggable("disable");

        // $("#drop-right .parent").droppable({
        $("#drop-right .parent").droppable({
            drop: function (event, ui) {
                console.log("drop-right")
                var draggableId = ui.draggable.find('img').attr("id");
                var aId2 = ui.draggable.find('a').attr("id");
                var droppableId = $(this).attr("id");
                var draggableUrl = ui.draggable.find('img').attr("src");
                var fetchedInitialPosition2 = ui.draggable.find('#initial-position').text();

                $('#drop-right').find('img').attr('src', draggableUrl);
                $('#drop-right').find('img').remove();
                $('#pan2').prepend('<img class="picture" src=' + draggableUrl + ' pictureOrderId = ' + draggableId + ' />');

                pictureInPanner(fetchedInitialPosition2, "right");
            }
        });
    });

//---------------------------------------------Button listeners---------------------------------------------------------------------------------------

    $('#left-reproduction-link').on('click', function () {        //sends user to new tab where picture may be seen in full
        var newWindow = window.open("pictureViewer.php");        //opening new document
        var url = $('#left-reproduction-link').attr('href');      //fetching url of picture
        newWindow.data = url;
        newWindow.colour = $('body').css("background-color");
    });

    $('#original-link').on('click', function () {        //sends user to new tab where picture may be seen in full
        var newWindow = window.open("pictureViewer.php");        //opening new document
        var url = $('#original-link').attr('href');      //fetching url of picture
        newWindow.data = url;
        newWindow.colour = $('body').css("background-color");
    });

    $('#right-reproduction-link').on('click', function () {        //sends user to new tab where picture may be seen in full
        var newWindow = window.open("pictureViewer.php");        //opening new document
        var url = $('#right-reproduction-link').attr('href');      //fetching url of picture
        newWindow.data = url;
        newWindow.colour = $('body').css("background-color");
    });

    $("#button-next").on('click', function () {              //REMOVE?
        //finished();
    });

    $('#quit').click(function () {       //If user confirms cancel he is returned to main page
        window.location = 'index.php';
    });

    $('#quit2').click(function () {       //If user confirms cancel he is returned to main page
        window.location = 'index.php';
    });

    $('#button-next').click(function () {       //If user confirms cancel he is returned to main page
        //postRating();
        loadExperiment();
    });

    $('#button-finished').click(function () {       //If user confirms cancel he is to return to main page

        if (isAllVisited()) {
            loadExperiment();
        }
        else {
            $('#popupButtons4').css({"margin-left": "-7.5%"});
            $('#contactArea4').find('p').text("");

            centerPopup4();
            loadPopup4();
        }
    });

    $('#button-next-rating').click(function () {
        loadExperiment();

    });

//----------------------------------------------------------------------------------------------------------------------------------------------------  

    getExperimentIdPost();
    experimentType(experimentId);        //checks experiment

    //console.log($( window ).width());
    //
    //if($( window ).width() <= 1366)    {
    //    adjustScaling();
    //}
    //
    //$( window ).resize(function() {
    //
    //
    //    if($( window ).width() < 1366)  {
    //        adjustScaling();
    //    }
    //
    //});


    if (type == 1) {
        postStartData(experimentId);
        getSpecificExperimentData(experimentId);
        startNewExperimentForObserver(experimentId);
        loadExperiment();
        updateSortablePosition();
        hideIndicators();
        //indicates which two pictures are default loaded into panner. (Always number 1 and 2)
        pictureInPanner("A", "left");
        pictureInPanner("B", "right");
    }

    IESpecific();

});
//----------------------------------------------------------------------------------------------------------------------------------------------------  

// function allowDrop(ev) {
//     ev.preventDefault();
//
// }
//
// function drop(ev) {
//     ev.preventDefault();
//     // var data = ev.dataTransfer.getData("text");
//     // ev.target.appendChild(document.getElementById(data));
//
//     // console.log(ev)
//     console.log(ev.dataTransfer.getData(this))
//     // console.log(ev.srcElement.currentSrc)
//
//     // "http://localhost/quickeval/uploads/1/103/o_1b07i57et5i16vi1o0s11csf6i1j.jpeg"
// }
//
// function drag(ev) {
//     // ev.dataTransfer.setData("text", ev.target.id);
//
//     console.log(ev)
//
//     dragged = ev;
// }
//
// var dragged;

/**
 * Loops through all images in sortable and getting their id and position
 * @returns {undefined}
 */
function updateSortablePosition() {
    var ids = $('#rating-images > div').map(function (i) {       //getting all elements id.
        return this.id;
    }).get();

    var pos = $('#rating-images > div').map(function (i) {       //getting all the elements position.
        return jQuery(this).index();
    }).get();

    var arrayLength = ids.length;              //get's length of the id array.

    for (var i = 0; i < arrayLength; i++) {             //goes through array and calls function which sets 
        // new position indicator for each.
        setPosition(ids[i], pos[i] + 1); //pos+1 because it starts on 0.
    }
}

/**
 * Sets new position for a image
 * @param {type} id of picture
 * @param {type} position the new position
 * @returns {undefined}
 */
function setPosition(id, position) {
    $("#rating-images #" + id + " .style-p ").text(position);
}

/**
 * Load received instruction into instruction area.
 * @param {type} instruction
 * @returns {undefined}
 */
function loadInstruction(instruction) {
    $('#contactArea2').empty();
    $('#contactArea2').append("<br/><strong>Instruction</strong><br/><br/>");
    $('#contactArea2').append(instruction);
}

/**
 * Removes middle container if original is not being used for comparison,
 * and add a class for repositioning the reproductions.
 * @returns {undefined}
 */
function removeOriginal() {
    $('#original').remove();
    $('#rating-container').removeAttr("style");
    $('#rating-container').addClass('remove-original');
    $('#original-tag').remove();
}

/**
 * Receives objects which contains all images and their data
 * @returns {undefined}
 */
function loadReproductionsSortable(data) {
    var length;
    var initialPosition;
    var letterCounter = 0;

    $('#rating-images').empty();                //empties div for the next pictures to be loaded
    length = Object.keys(data).length - 1;

    for (var i = 1; i <= length; i++) {                         //goes through all objects getting their data.
        var reproductionImageUrl = data[i].url;                 //getting url
        var reproductionPictureOrder = data[i].pictureOrderId;  //getting id

        if (i == 1) {              //first picture gets loaded into left panner.
            loadImageIntoPanner(reproductionPictureOrder, reproductionImageUrl, "left");
        }

        if (i == 2) {              //second picture get loaded into right panner.
            loadImageIntoPanner(reproductionPictureOrder, reproductionImageUrl, "right");
        }


        initialPosition = String.fromCharCode('A'.charCodeAt(0) + letterCounter);
        // console.log(initialPosition);
        letterCounter++;

        //each picture is appended to the sortable
        //$('#rating-images').append('<div class="image-position" id=' + i + '><p class="style-p" >1</p><img src=' + reproductionImageUrl + ' id=' + reproductionPictureOrder + ' ><br><span id="initial-position">' + initialPosition + '</span></div>');
        $('#rating-images').append('<div class="image-position tile-sortable" id=' + i + '><span class="style-p">1</span><img src=' + reproductionImageUrl + ' id=' + reproductionPictureOrder + ' ><br><span id="initial-position">' + initialPosition + '</span></div>');
    }


    retinaSpecific();
    updateSortablePosition();
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

var ratingRunned;
/**
 * For when there is a new instruction, calls functions in popup.js
 * @returns {undefined}
 */
function onlyInstruction() {
    centerPopup2();         //centering with css
    loadPopup2();           //load popup
}

/**
 * Goes to next step in current experiment
 * @returns {undefined}
 */
function loadExperiment() {
    var data = getNextInExperimentForObserver();
    var dimensions;

    if (data['type'] == "experimentinstruction") {              //is instruction
        var instruction = data['experimentinstruction'];        //get's the instruction
        loadInstruction(instruction);                           //sends instruction to function for loading
        onlyInstruction();                                      //displays instruction to user
        loadExperiment();                                       //goes to the next step which in most cases is pictures.
    }

    if (data['type'] == "pictureQueue") {                       //is picture set 

        if (ratingRunned == 1) {
            postRating();
        }

        loadReproductionsSortable(data);                        //get's all images in the experiment
        var originalImageUrl = data[1]['originalUrl'].url;      //getting url of original image

        panningCheck(originalImageUrl);
        loadOriginal(originalImageUrl);                         //loading original picture
        ratingRunned = 1;

    }

    if (data['type'] == "finished") {                           //user is finished
        experimentComplete(experimentId);
        finished();
        $("#button-finished").text("Finish");
        $('#contactArea').empty();                              //changes content of popup
        $('#continue').hide();
        $('#contactArea').append("You have finished, thank you for your time <br><br> Click Quit to return to front page.");
        // $('#cancel-experiment').trigger('click');
    }
}


/**
 *  Applies a indicator for which picture in is loaded into panner
 * @param initPos the initial position of an image IDed by a letter.
 * @param side which panner to update.
 */
function pictureInPanner(initPos, side) {


    // console.log(initPos);

    if (side == "left") {
        $('#picture-in-panner-left').find('span strong').text(initPos);

    }
    else if (side == "right") {
        $('#picture-in-panner-right').find('span strong').text(initPos);

    }
}

/**
 * Hides all image in panner indicator so that only those who are suppose to be visible is visible.
 * @returns {undefined}
 */
function hideIndicators() {
    var numItems = $('.image-position').length;     //gets the number of divs with particular class

    for (var i = 1; i <= numItems; i++) {
        $('image, #' + i + '').find('a').hide();
    }
}

/**
 * Loops through all matching divs and checks,
 * whether they have been visited or not.
 * Returns true if all have been visited(touched).
 * @returns {Boolean} Returns false if one is not visited.
 */
function isAllVisited() {
    var numItems = $('.image-position').length;     //gets the number of divs with particular class

    for (var i = 1; i <= numItems; i++) {
        var hasClass = $('image-position, #' + i + '').hasClass('touched');
        if (!hasClass) {
            return false;           //one div has not been touched.
        }
    }
    return true;                //all div have been visited.
}

/**
 * User is has clicked finished.
 * Function post results and returns user to front page.
 * @returns {undefined}
 */
function finished() {
    if (isAllVisited()) {           //all have been visited, if valid user is at this stage considered finished.
        postRating();
        //changes content of popup dependent on whether user has visited all or not.
        $('#contactArea').empty();
        $('#popupButtons3').css({"margin-left": "-3.5"});
        $('#contactArea').append("You have finished, thank you for your time <br><br> Click Quit to return to front page.");
        $('#cancel-experiment').trigger('click');
    }
    else {
        $('#popupButtons3').css({"margin-left": "-7.5%"});
        $('#contactArea3').append("You sure you want to finish?, All pictures haven't been sorted <br><br> Click Quit to return to front page or Continue to keep sorting.");

        centerPopup3();
        loadPopup3();

        $('#quit3').click(function () {       //If user confirms cancel he is returned to main page
            postRating();
            window.location = 'index.php';
        });
    }
}

/**
 * Gets the information about pictures in sortable elements in sequence and for later insertion into DB.
 * @returns {undefined}
 */
function postRating() {
    $('.image-position').each(function () {      //loops through all div with matching class
        pictureOrderId = $(this).find('img').attr('id');        //get's Id of the image, which is the pictureOrderId

        postResultsRating(experimentId, pictureOrderId);      //for each there is posted data to DB.
    });
}

/**
 * Loads the image which is considered original into middle panner.
 * @param {type} originalImageUrl url of the image on the server.
 * @returns {undefined}
 */
function loadOriginal(originalImageUrl) {
    $('#original').find('img').attr('src', originalImageUrl);
    $('#original-link').attr('href', originalImageUrl); //Adding right top corner link
}

/**
 * Receives data about a picture and loads it into either panner.
 * @param {type} pictureOrderId id of order of picture, added for later reference.
 * @param {type} imageUrl address of picture on the server
 * @param {type} side tells function which panner picture is to be loaded in.
 * @returns {undefined}
 */
function loadImageIntoPanner(pictureOrderId, imageUrl, side) {

    if (side == "left") {
        $('#pan1').find('img').attr('src', imageUrl);
        $('#pan1').find('img').attr('pictureOrderId', pictureOrderId);
        $('#left-reproduction-link').attr('href', imageUrl);
    }
    else {
        $('#pan2').find('img').attr('src', imageUrl);

        $('#pan2').find('img').attr('pictureOrderId', pictureOrderId);
        $('#right-reproduction-link').attr('href', imageUrl);
    }
}

/**
 * Recives data for posting to DB.
 * @param {type} experimentId id of current experiment.
 * @param {type} pictureOrderId id of the image's id in the experiment.
 * @returns {undefined}
 */
function postResultsRating(experimentId, pictureOrderId) {
    $.ajax
    ({
        url: 'ajax/observer/insertIntoResultRating.php',
        async: false,
        data: {
            'type': "rating",
            'experimentId': experimentId,
            'pictureOrderId': pictureOrderId,
        },
        type: 'post',
        success: function (data) {
        },
        error: function (request, status, error) {
        }
    });
}

/**
 * Checks if the experiment is of right type.
 * @param {type} experimentId id of experiment.
 * @returns {undefined}
 */
function experimentType(experimentId) {
    $.ajax
    ({
        url: 'ajax/observer/getExperimentType.php',
        async: false,
        data: {
            'experimentId': experimentId,
        },
        type: 'post',
        dataType: 'json',
        success: function (data) {
            if (data[0]['type'] == "rating")
                updateType();
        },
        error: function (xhr, ajaxOptions, thrownError) {
//                    console.log("Error");
//                    console.log(xhr.status);
//                    console.log(thrownError);
        }
    });
}

/**
 * Updates a global variable after ajax has checked experiment type.
 * @returns {undefined}
 */
function updateType() {
    type = 1;
}


function retinaSpecific() {
    var retina = (window.retina || window.devicePixelRatio > 1);
    if (retina) {
        // console.log("Retina detected");
        $('body').addClass('retina'); // for example
        $(".image-position img").each(function () {
            // console.log(this);
            $(this).css({'height': '100px', 'width': '100px'});
        });
    }
    else {
        console.log("Retina NOT detected");
        //rescales images for fitment
        $(".image-position img").each(function () {
            console.log(this);
            $(this).css({'height': '150px', 'width': '150px'});
        });

    }
}
