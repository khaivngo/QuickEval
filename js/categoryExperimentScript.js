
$(document).ready(function() {

    (function () {
        var $section = $('#set1, #set2, #set3');
        $section.find('.panzoom').panzoom({
            $zoomIn: $section.find(".zoom-in"),
            $zoomOut: $section.find(".zoom-out"),
            $zoomRange: $section.find(".zoom-range"),
            $reset: $section.find(".reset"),
            $set: $section.find('.parent > div'),
            contain: 'invert',
            minScale: 1,
            maxScale: 1.21
        }).panzoom('zoom');
    })();



    $('#reproduction-link').on('click', function() {        //sends user to new tab where picture may be seen in full
        var newWindow = window.open("pictureViewer.php");        //opening new document
        var url = $('#reproduction-link').attr('href');      //fetching url of picture
        newWindow.data = url;
        newWindow.colour = $('body').css("background-color");
    });

    $("#button-next-category").on('click', function() {
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
    startNewExperimentForObserver(experimentId);
    loadExperiment2();
    fillCategories();

});

/**
 * Appends all categories for the step into an select for a dropdown menu.
 * @returns {undefined}
 */
function fillCategories() {
    var categoryArray = getCategoriesForObserverExperiment(experimentId);
    var i = 0;
    var options = $("#categories");

    categoryArray.forEach(function() {
        options.append($("<option />").val(categoryArray[i]['id']).text(categoryArray[i]['name']));
        i++;
    });
}

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

var runned = 0;
var test= 0;
/**
 * Goes to next step in current experiment
 * @returns {undefined}
 */
function loadExperiment2(data) {
    var data;
     
     //console.log("TEST: "+(test++));

    if (runned <= 1) {
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
        //console.log("is pictureQueue this is picture: "+data[1].url);

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

    runned = 1;
}

/**
 * Loads the original picture into the panner and sets fullscreen link.
 * @param {type} originalImageUrl url of original image.
 * @returns {undefined}
 */
function loadOriginal(originalImageUrl) {
    $('#original').find('img').attr('src', originalImageUrl);
    $('#original-link').attr('href', originalImageUrl); //Adding right top corner link
}

/**
 * Loads reproduction image into panner and updates fullscreen link.
 * @param {type} pictureOrderId id of order of the picture.
 * @param {type} reproductionImageUrl url to image.
 * @returns {undefined}
 */
function loadCategoryReproduction(pictureOrderId, reproductionImageUrl) {
    $('#reproduction').find('img').attr('src', reproductionImageUrl);
    $('#reproduction').find('img').attr('id', pictureOrderId);
    $('#reproduction-link').attr('href', reproductionImageUrl);
}

/**
 * Receives data about chosen picture and category for insertion in DB.
 * @param {type} experimentId id of current experiment.
 * @param {type} pictureOrderId id of order of the picture.
 * @param {type} category number of the category.
 * @returns {undefined}
 */
function postResultCategory(experimentId, pictureOrderId, category) {
    $.ajax
            ({
                url: 'ajax/observer/insertIntoResultCategory.php',
                async: false,
                data: {'type': "category",
                    'experimentId': experimentId,
                    'pictureOrderId': pictureOrderId,
                    'category': category
                },
                type: 'post',
                success: function(data) {

                },
                error: function(request, status, error) {
                }
            });
}

/**
 * Goes to the next step for experiment.
 * @returns {undefined}
 */
function nextImageCategory(ifPost) {
    var pictureOrderId = $('#reproduction').find('img').attr('id');

    $("#myselect option:selected").text();

    var categoryId = $("#categories option:selected").val();

    if(ifPost == 1)
    postResultCategory(experimentId, pictureOrderId, categoryId);

    //var data = getNextInExperimentForObserver();
    loadExperiment2();
}

/**
 * Loads instruction into popup.
 * @param {type} instruction
 * @returns {undefined}
 */
function loadInstruction2(instruction) {
    //var instruction = "Se på dette fine bildet";

    $('#contactArea2').empty();
    $('#contactArea2').append(instruction);
}