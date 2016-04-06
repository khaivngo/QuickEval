/**
 * Starts a new experiment for a user, and this function
 * will make everything ready, and store the queue in session
 * @param  experimentId is the ID of the experiment to start.
 */
function startNewExperimentForObserver(experimentId) {
    $.ajax
    ({
        url: 'ajax/observer/experimentQueue.php',
        async: false,
        data: {
        	'option':'startNewObserverExperiment',
        	'experimentId':experimentId
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Gets the next position in the experiment for a observer.
 * @return instruction with text, the pictures which are to be shown,
 * or a text with "finished" that indicates that an experiment is over.
 *
 */
function getNextInExperimentForObserver() {
	var returnData = new Array();
    $.ajax
    ({
        url: 'ajax/observer/experimentQueue.php',
        async: false,
        data: {
        	'option':'getNextPosition',
        },
        dataType: 'json',
        success: function(data)
        {
            if (data.type != "finished") {
                getCurrentPictureQueue(
                    data.pictureQueue[0].pictureId,
                    data.pictureQueue[0].pictureOrderId
                );
            }

        	if(data.type == "pictureQueue") {
        		returnData = {type : "pictureQueue"};
	            for(var i = 0; i < data.pictureQueue.length; i++) {
	            	returnData[i+1] = getUrlForPicture(data.pictureQueue[i]);
	            }
        	} else if(data.type == "finished") {
        		returnData = data;
        	} else if (data.type == "experimentinstruction") {
        		returnData = data;
        	}
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return returnData;
}

function getCurrentPictureQueue(pictureID, pictureOrderID) {
    $.ajax({
        url: 'ajax/observer/getCurrentPictureQueue.php',
        type: 'POST',
        data: {
            pictureID: pictureID,
            pictureOrderID: pictureOrderID
        },
        dataType: 'json',
        encode: true,
        cache: false,
        processData: true
    })
    .done(function(response) {
        $('.canvas-container').attr('data-picture-queue', response[0].pictureQueue);
        $(document).trigger('data-attribute-changed');
        // $(document).ready(function() {
        //     $('.canvas-container').canvasMarkingTool({
        //         annotation: true
        //     });
        // });
    })
    .fail(function(response) {
        console.log(response.responseText);
    });
}

/**
 * Gets URL for a given pictureId together with pictureorder,
 * id and url.
 * @param pictureId ID of the picture you want data for.
 * @return array containing picture information with url, pictureOrder and id.
 */
function getUrlForPicture(pictureId) {
	var pictureUrl = new Array();
    $.ajax
    ({
        url: 'functions.php',
        async: false,
        data: {
        	'option':'getPictureUrl',
        	'pictureId':pictureId
        },
        dataType: 'json',
        success: function(data)
        {
            pictureUrl = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return pictureUrl;
}
/**
 * Will get all categories for an experiment.
 * @param experimentId contains ID for the given experiment you want to get categories for.
 * @return an array with all categories for the experiment.
 */
function getCategoriesForObserverExperiment(experimentId)
{
	var categories = new Array();
    $.ajax
    ({
        url: 'ajax/observer/getExperimentInformation.php',
        async: false,
        data: {
        	'option':'getCategoriesForExperiment',
        	'experimentId':experimentId
        },
        dataType: 'json',
        success: function(data)
        {
            categories = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return categories;
}
