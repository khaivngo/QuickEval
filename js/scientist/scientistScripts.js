/**
 * Gets imagesets. Sorts them based on parameter, by name if no parameter,
 * by date if true
 * @param {type} $sort Will sort by date if true
 * @returns {data|getImageSets.imageSets}   Two dimensional array containing image sets and data
 */
function getImageSets($sort) {
    var imageSets;
    $.ajax({
        url: 'ajax/scientist/getImageSets.php',
        async: false,
        dataType: 'json',
        success: function (data) {
            imageSets = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    if ($sort == true) { //Returns a unsorted list if sorting by date
        return imageSets;
    }

    imageSets.sort(function (a, b) { //Sorts the list based on name
        if (a.name < b.name) {
            return -1;
        }
        if (a.name > b.name) {
            return 1;
        }
        return 0;
    });
    return imageSets;
}

/**
 * @return Gets an array of all earlier instructions for inlogged scientist.
 */
function getInstructions() {
    var instructions;
    $.ajax({
        url: 'ajax/scientist/getInstructions.php',
        async: false,
        data: {
            'option': 'getInstructions'
        },
        dataType: 'json',
        success: function (data) {
            instructions = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    return instructions;
}

/**
 * Rturns all inputfields from logged in user
 * @return {array} array with inputfields
 */
function getInputfields() {
    var inputfields;
    $.ajax({
        url: 'ajax/scientist/getInputfields.php',
        async: false,
        data: {
            'option': 'getInputfields'
        },
        dataType: 'json',
        success: function (data) {
            inputfields = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    return inputfields;
}

/**
 * Function makes a new experiment and stores it in the database.
 * Stores data from step 1 in experiment creation.
 * @return Id of the newly created experiment.
 */
function startNewExperiment(name, shortDescription, description, exType,
                            screenWhitePoint, screenLuminance, roomIllumination, ambientIllumination, viewingDistance) {
    var experimentId;
    $.ajax({
        url: 'ajax/scientist/experimentFunctions.php',
        async: false,
        data: {
            'option': 'newExperiment',
            'name': name,
            'shortDescription': shortDescription,
            'description': description,
            'exType': exType,
            'screenWhitePoint': screenWhitePoint,
            'screenLuminance': screenLuminance,
            'roomIllumination': roomIllumination,
            'ambientIllumination': ambientIllumination,
            'viewingDistance': viewingDistance
        },
        datatype: 'json',
        success: function (data) {
            experimentId = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
            experimentId = 0; //If error
        }
    });
    return experimentId.replace(/"/g, "");
}

/**
 * @returns Gets the ID for the active experiment which is in creation.
 */
function getActiveExperimentInCreation() {
    var experimentId;
    $.ajax({
        url: 'ajax/scientist/experimentFunctions.php',
        async: false,
        data: {
            'option': 'getExperimentId'
        },
        datatype: 'json',
        success: function (data) {
            experimentId = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    return experimentId.replace(/"/g, "");
}

/**
 * Will alter required data for a experiment.
 * The params are all standard parametres except for the customFields.
 * @param customFields is an array containing ID's of the custom fields
 * that the user wants to add.
 */
function addObserverInputFields(defaultFields, customFields, experimentId) {
    $.ajax({
        url: 'ajax/scientist/experimentFunctions.php',
        async: false,
        data: {
            'option': 'addObserverInputFields',
            'defaultFields': defaultFields,
            'customFields': customFields
        },

        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Adds parameters to a experiment!
 * @param forcePick true or false
 * @param samePairTwice true or false
 * @param showOriginal true or false
 * @param experimentId the Id of the experiment to use.  Will use the last one
 * in the database if no experimentID is supplied.
 */
function addObserverParametersPair(backgroundColour, forcedPick, samePairTwice, showOriginal, colorblind, hidden, timer, experimentId) {

    if (experimentId == 0 || experimentId == null) {
        experimentId = getActiveExperimentInCreation();
    }
    if (timer == 0 || timer == null) {
        timer = 0;
    }

    console.log("fetched color = " + backgroundColour);
    $.ajax({
        url: 'ajax/scientist/experimentFunctions.php',
        async: false,
        data: {
            'option': 'addParametersPair',
            'backgroundColour': backgroundColour,
            'experimentId': experimentId,
            'forcedPick': forcedPick,
            'samePairTwice': samePairTwice,
            'showOriginal': showOriginal,
            'colorblind': colorblind,
            'hidden': hidden,
            'timer': timer
        },
        success: function (data) {
            //console.log(data);
        },

        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Will add a pictureQueue to experimentOrder in the database.
 * @param pictureQueueId the ID of the pictureQueue which you want to add to the experimentOrder
 * @param experimentId the Id of the experiment to use.  Will use the last one
 * in the database if no experimentID is supplied.
 */
function addPictureQueueToExperiment(pictureQueueId, experimentId) {
    if (experimentId == null || experimentId == 0) {
        experimentId = getActiveExperimentInCreation();
    }
    $.ajax({
        url: 'ajax/scientist/experimentFunctions.php',
        async: false,
        data: {
            'option': 'addPictureQueueOrInstruction',
            'experimentId': experimentId,
            'pictureQueueId': pictureQueueId
        },

        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Will create a new experimentOrder in the database for the given instruction
 * @param instructionId Id of the experimentInstruction to add to the experimentOrder
 * @param experimentId the Id of the experiment to use.  Will use the last one
 * in the database if no experimentID is supplied.
 */
function addInstructionToExperiment(instructionId, experimentId) {
    if (experimentId == 0 || experimentId == null) {
        experimentId = getActiveExperimentInCreation();
    }
    $.ajax({
        url: 'ajax/scientist/experimentFunctions.php',
        async: false,
        data: {
            'option': 'addPictureQueueOrInstruction',
            'experimentId': experimentId,
            'experimentInstructionId': instructionId
        },

        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Gets the earlier pictureQueues for a given pictureSet
 * @param pictureSetId the ID for a pictureSet you want to find every pictureQueue for
 * @return array of the pictureQueues with ID and name.
 */
function getEarlierPictureQueues(pictureSetId) {
    var pictureQueues = [];

    $.ajax({
        url: 'ajax/scientist/getEarlierPictureQueues.php',
        async: false,
        data: {
            'pictureSetId': pictureSetId
        },
        dataType: 'json',
        success: function (data) {
            pictureQueues = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });

    return pictureQueues;
}

/**
 * Checks if pictureset has an original
 * @param  {int}  pictureSetId id of pictureset
 * @return {int}              0 if no original
 */
function hasOriginal(pictureSetId) {
    var bool;

    $.ajax({
        url: 'ajax/scientist/hasOriginal.php',
        async: false,
        data: {
            'pictureSetId': pictureSetId
        },
        dataType: 'json',
        success: function (data) {
            bool = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
            bool = 0;
        }
    });

    return bool;
}

/**
 * Creates a new category and connects it to experiment
 * @param {string} categoryText text of category
 * @param {int} experimentId id of experiment
 */
function addCategory(categoryText, experimentId) {
    $.ajax({
        url: 'ajax/scientist/addCategory.php',
        async: false,
        data: {
            'categoryText': categoryText,
            'experimentId': experimentId
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Adds a existing category to experiment
 * @param {int} categoryNameId id of category
 * @param {int} experimentId   id of experiment
 */
function addExistingCategory(categoryNameId, experimentId) {
    $.ajax({
        url: 'ajax/scientist/addExistingCategory.php',
        async: false,
        data: {
            'categoryNameId': categoryNameId,
            'experimentId': experimentId
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Gets all categories of logged in user
 * @return {array} array of categories
 */
function getCategories() {
    var categories = Array();
    $.ajax({
        url: 'ajax/scientist/getCategory.php',
        async: false,
        dataType: 'json',
        success: function (data) {
            categories = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
    return categories;
}

/**
 * Gets all experiments which are public
 * @return {array} array of experiments
 */
function getExperiments() {
    var result;

    $.ajax({
        url: 'ajax/scientist/getExperiments.php',
        async: false,
        dataType: 'json',
        success: function (data) {
            result = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
            result = 0;
        }
    });

    return result;
}

/**
 * Gets experimentdata by id
 * @param  {int}    $experimentId   id of experiment
 * @return {array}  array with experimentdata
 */
function getExperimentById($experimentId) {
    var result;

    $.ajax({
        type: "POST",
        url: 'ajax/scientist/getExperimentById.php',
        async: false,
        data: {
            'experimentId': $experimentId,
        },
        dataType: 'json',
        success: function (data) {
            result = data;
        },
        error: function (request, status, error) {
            result = null;
        }
    });

    return result;
}

/**
 * Get all instructions for an experiment.
 */
function getExperimentsInstructionsById($experimentId) {
    var result;

    $.ajax({
        type: "POST",
        url: 'ajax/scientist/getExperimentInstructionsById.php',
        async: false,
        data: {
            'experimentId': $experimentId
        },
        dataType: 'json',
        success: function (data) {
            result = data;
        },
        error: function (request, status, error) {
            alert(request.responseText);
            result = 0;
        }
    });

    return result;
}

/**
 * Deletes experiment based on id
 * @param  {int} experimentId id of experiment
 */
function deleteExperiment(experimentId) {
    $.ajax({
        type: 'POST',
        url: 'ajax/admin/deleteExperiment.php',
        data: {
            'experimentId': experimentId
        },
        dataType: 'json',
        success: function (data) {
            console.log(data)
            if (data == 1) { //that particular experiment was successfully deleted.
                $.Notify({ //notifies user about successfull experiment deletion change.
                    content: "Experiment successfully deleted",
                    style: {
                        background: 'lime'
                    }
                });
            }
        },
        error: function (request, status, error) {
            alert(request.responseText);
        }
    });
}

