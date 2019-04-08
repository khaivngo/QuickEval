$(document).ready(function () {

    (function () {
        var $section = $('#set1, #set2');
        $section.find('.panzoom').panzoom({
            $zoomIn: $section.find(".zoom-in"),
            $zoomOut: $section.find(".zoom-out"),
            $zoomRange: $section.find(".zoom-range"),
            $reset: $section.find(".reset"),
            $set: $section.find('.parent > div'),
            minScale: 1,
            maxScale: 1
        }).panzoom('zoom');
    })();

    $('#panzoom-reset').click(function () {
        $("#set1 .panzoom, #set2 .panzoom, #set3 .panzoom").panzoom("resetPan");
    });

    $('#reproduction-link').on('click', function () {        //sends user to new tab where picture may be seen in full
        var newWindow = window.open("pictureViewer.php");        //opening new document
        var url = $('#reproduction-link').attr('href');      //fetching url of picture
        newWindow.data = url;
        newWindow.colour = $('body').css("background-color");
    });

    $("#button-next-category").on('click', function () {
        var selected = $('#categories option').filter(':selected').val();
        if (selected == "null") {       //if the option is not a valid category = option is disabled
            $.Notify({
                content: "Please select a category...",
                style: {
                    background: 'darkred',
                    color: 'white'
                },
            });
        }
        else {              //valid option goes next.
            nextImageCategory(1);
            //console.log("NextImageCategory");
            $("#categories").val('null');
        }
    });

    getExperimentIdPost();
    getSpecificExperimentData(experimentId);
    postStartData(experimentId);
    deleteOldResults(experimentId);
    deleteOldArtifactMarks(experimentId);
    startNewExperimentForObserver(experimentId);
    loadExperiment2();
});

/**
 * Removes middle container if original is not being used for comparison,
 * and add a class for repositioning the reproductions.
 * @returns {undefined}
 */
function removeOriginal2() {
    $('#original').remove();
    $('#category-container').removeAttr("style");
    $('#category-container').removeAttr('class');
    $('#category-container').addClass('only-original');
    $('#reproduction').removeAttr('class');
    $('#reproduction').addClass('reproduction-without-original');
    $('#original-tag').remove();
}

var step = 0;
/**
 * Goes to next step in current experiment
 * @returns {undefined}
 */
function loadExperiment2(data) {
    var data;

    if (step <= 1) {
        data = getNextInExperimentForObserver();
    }

    if (data['type'] == "experimentinstruction") {                //is instruction
        var instruction = data['experimentinstruction'];
        loadInstruction(instruction);
        onlyInstruction();
        // data = getNextInExperimentForObserver();
        nextImageCategory(0);
    }

    if (data['type'] == "pictureQueue") {                   //picture set
        var originalImageUrl = data[1]['originalUrl'].url; //getting url of original image
        panningCheck(originalImageUrl);
        loadOriginal(originalImageUrl); // calls function for setting image.

        var reproductionPictureOrder = data[1].pictureOrderId;
        var reproductionImageUrl = data[1].url;

        loadCategoryReproduction(reproductionPictureOrder, reproductionImageUrl);
    }

    if (data['type'] == "finished") {           //user is finished
        experimentComplete(experimentId);
        $('#popupButtons3').css({"margin-left": "-3.5%"});
        $('#contactArea3').append("You have finished, thank you for your time <br><br> Click Quit to return to front page.");
        centerPopup3();
        loadPopup3();
    }

    step = 1;
}

/**
 * Loads the original picture into the panner and sets fullscreen link.
 * @param {type} originalImageUrl url of original image.
 * @returns {undefined}
 */
function loadOriginal(originalImageUrl) {
    $('#original').find('img').attr('src', originalImageUrl);
    $('#original-link').attr('href', originalImageUrl); // Adding right top corner link
}

/**
 * Loads reproduction image into panner and updates fullscreen link.
 * @param {type} pictureOrderId id of order of the picture.
 * @param {type} reproductionImageUrl url to image.
 * @returns {undefined}
 */
function loadCategoryReproduction(pictureOrderId, reproductionImageUrl)
{
    $('#reproduction').find('.canvas-container').attr('data-image-url', reproductionImageUrl);
    $('#reproduction').find('.canvas-container').attr('id', pictureOrderId);
    $('#reproduction-link').attr('href', reproductionImageUrl);

    /* calculate the canvas stuff whenever a new image is loaded */
    $(document).ready(function() {
        /* remove any previously added images, if this is the second time we call this function */
        $('.canvas-container').empty();

        $('.canvas-container').canvasMarkingTool({
            annotation: true,
            showToolbar: true
        });
    });

}

/**
 * Receives data about chosen picture and category for insertion in DB.
 * @param {type} experimentId id of current experiment.
 * @param {type} pictureOrderId id of order of the picture.
 * @param {type} category number of the category.
 * @returns {undefined}
 */
function postResultCategory(experimentId, pictureOrderId, category) {
    $.ajax({
        url: 'ajax/observer/insertIntoResultCategory.php',
        async: false,
        data: {
            'type': "artifact",
            'experimentId': experimentId,
            'pictureOrderId': pictureOrderId,
            'category': category
        },
        type: 'post',
        success: function (data) {
            //
        },
        error: function (request, status, error) {
            //
        }
    });
}

/**
 * Goes to the next step for experiment.
 * @returns {undefined}
 */
function nextImageCategory(ifPost) {
    var pictureOrderId = $('#reproduction').find('.canvas-container').attr('id');

    $("#myselect option:selected").text();

    var categoryId = $("#categories option:selected").val();

    if (ifPost == 1) {
        postResultCategory(experimentId, pictureOrderId, categoryId);
    }

    loadExperiment2();
}

/**
 * Loads instruction into popup.
 * @param {type} instruction
 * @returns {undefined}
 */
function loadInstruction2(instruction) {
    $('#contactArea2').empty();
    $('#contactArea2').append(instruction);
}


/**
 * Delete all artifact marks belonging to a experiment.
 */
function deleteOldArtifactMarks(experimentId) {
    $.ajax({
        url: 'ajax/observer/deleteOldArtifactMarks.php',
        data: {
            'experimentId': experimentId
        },
        type: 'post',
        dataType: 'json',
        async: false,
        success: function (data) {
            console.log(data);
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}
