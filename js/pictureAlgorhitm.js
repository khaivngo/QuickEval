//Funksjon som generer bildekø for et helt bildesett når random funksjonen er valg for bildesettet.
function generateRandomPictureQueue(imagesetId, rightAndLeft) {
	var pictureQueueId;
    $.ajax
    ({
        url: 'ajax/scientist/setPictureQueue.php',
        async: false,
        data: {
        	'imagesetId'  :imagesetId,
        	'rightAndLeft':rightAndLeft,
        	'option':'generateRandom'
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
 * @param pictureQueueId is the queue which to add pictures to. if it is 0 then a new queue is begun.
 *
 */
function setPictureQueue(imagesArray, rightAndLeft, pictureQueueId) {
	var pictureQueueId;
    $.ajax
    ({
        url: 'ajax/scientist/setPictureQueue.php',
        async: false,
        data: {
        	'images'  :imagesArray,
        	'rightAndLeft':rightAndLeft,
        	'pictureQueueId':pictureQueueId,
        	'option':'notRandom'
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
