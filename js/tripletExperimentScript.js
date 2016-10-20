$(document).ready(function () {

    $('#button-next-triplet').click(function () {
        var selected1 = $('#categories1 option').filter(':selected').val();
        var selected2 = $('#categories2 option').filter(':selected').val();
        var selected3 = $('#categories3 option').filter(':selected').val();

        // if the option is not a valid category = option is disabled
        if (selected1 == "null" || selected2 == "null" || selected3 == "null") {
            // $.Notify({
            //     content: "Please select a category...",
            //     style: {
            //         background: 'darkred',
            //         color: 'white'
            //     }
            // });
        }
        // valid option goes next.
        else {
            var pictureOrderId1 = $('#left-reproduction-link').attr('pictureorderid');
            var pictureOrderId2 = $('#middle-reproduction-link').attr('pictureorderid');
            var pictureOrderId3 = $('#right-reproduction-link').attr('pictureorderid');

            postResultCategory(experimentId, pictureOrderId1, selected1);
            postResultCategory(experimentId, pictureOrderId2, selected2);
            postResultCategory(experimentId, pictureOrderId3, selected3);

            // reset categories
            $(".categories").val('null');
            // go to next experiment
            nextComparison();
        }
    });


    // listen for changes to the categories dropdowns,
    // when they've all been completed activate the next button again
    $('.categories').click(function() {
        var selected1 = $('#categories1 option').filter(':selected').val();
        var selected2 = $('#categories2 option').filter(':selected').val();
        var selected3 = $('#categories3 option').filter(':selected').val();

        if (selected1 != "null" && selected2 != "null" && selected3 != "null") {
            activateNextButton();
        }
    });

    getExperimentIdPost();
    fillCategories();
});

/**
 * Appends all categories for the step into an select for a dropdown menu.
 * @returns {undefined}
 */
function fillCategories() {
    var categoryArray = getCategoriesForObserverExperiment(experimentId);
    var i = 0;
    var options = $(".categories");

    categoryArray.forEach(function () {
        options.append($("<option />").val(categoryArray[i]['id']).text(categoryArray[i]['name']));
        i++;
    });
}

var runned = 0;
var test = 0;

/**
 * Receives data about chosen picture and category for insertion in DB.
 * @param {type} experimentId id of current experiment.
 * @param {type} pictureOrderId id of order of the picture.
 * @param {type} category number of the category.
 * @returns {undefined}
 */
function postResultCategory(experimentId, pictureOrderId, category) {

    var ajaxSettings = {
        url: 'ajax/observer/insertIntoResultCategory.php',
        async: false,
        data: {
            'type': "triplet",
            'experimentId': experimentId,
            'pictureOrderId': pictureOrderId,
            'category': category
        },
        type: 'post'
    };

    $.ajax(ajaxSettings);
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
