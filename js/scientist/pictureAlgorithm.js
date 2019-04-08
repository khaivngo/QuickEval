/**
 * Makes a whole new random pictureQueue.
 *
 * @param imagesetId    ID to generate a random queue from.
 * @param rightAndLeft  0/1 if you want to show the queue twice, but changed positions.
 *
 * @return ID of the newly created pictureuQueue.
 */
function generateRandomPictureQueue(experimentType, imagesetId, rightAndLeft) {
	var pictureQueueId;

    $.ajax({
        url: 'ajax/scientist/setPictureQueue.php',
        async: false,
        data: {
        	'imagesetId': imagesetId,
        	'rightAndLeft': rightAndLeft,
        	'option': 'generateRandom',
			'experimentType': experimentType
        },
        dataType: 'json',
        success: function(data) {
        	pictureQueueId = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });

    return pictureQueueId;
}

/**
 * Generates pictureOrder for a set of images.
 *
 * @param   imagesArray     an array containing ID's of the pictures to make a queue from.
 * @param   rightAndLeft    0/1 if you want to show the queue twice, but changed positions.
 * @param   pictureQueueId  is the queue which to add pictures to. if it is 0 then a new queue is begun.
 * @param   name            name of the queue.
 *
 * @return  pictureQueueId of the active Queue which pictures was added to
 */
function setPictureQueue(imagesArray, rightAndLeft, pictureQueueId, name) {
	var pictureQueueId;

    $.ajax({
        url: 'ajax/scientist/setPictureQueue.php',
        async: false,
        dataType: 'json',
        data: {
        	'images': imagesArray,
        	'rightAndLeft': rightAndLeft,
        	'pictureQueueId': pictureQueueId,
        	'name': name,
        	'option': 'notRandom'
        },
        success: function(data) {
        	pictureQueueId = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });

    return pictureQueueId;
}

/**
 * Generates a new pictureQueue for rating or category experiments.
 *
 * @param imagesetId  is the ID of the set we get all the pictures from.
 * @param name        of the pictureQueue
 *
 * @return the newly generated pictureQueueId
 */
function setPictureQueueRatingCategory(imagesetId) {
	var pictureQueueId;

    $.ajax({
        url: 'ajax/scientist/setPictureQueue.php',
        async: false,
        data: {
        	'imagesetId': imagesetId,
        	'option': 'ratingCategory'
        },
        dataType: 'json',
        success: function(data) {
        	pictureQueueId = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });

    return pictureQueueId;
}
