$(document).ready(function () {

    $("#upload-image").click(function () {
        $('#right-menu').empty();   //Empties right menu if user came from function using menu

        setActive($(this));
        enterName($('#right-panel'));
        addOriginalButtonListener();

    });
});

function enterName(target) {
    target.empty();
    var container = $('<div>');
    container.load('ajax/scientist/uploadImages.html', function () {
        target.prepend(container)
        $('#start-upload-button').click(function () {
            $('.notice').remove();
            if ($("#image-set-name").val().length > 2) {
                $("#image-set-description").prop('disabled', true);
                $("#image-set-name").prop('disabled', true);
                $(this).prop('disabled', true);
                startUploader(0, $('#right-panel'));
            } else {
                $('.notice').remove();
                $("#image-set-name").after('<div id="notify"><div class="span1" style="margin: 20px"></div>' +
                '<div class="bg-red notice marker-on-top span1">' +
                'Name needs to be 3 characters or longer' +
                '</div></div>');
            }

        });
    });
}


/**
 * @param name is the string for the name of the set.
 * @param text ----""-----
 * @param imagesetId = 0 if it is to start a new set.
 */
function startUploader(imagesetId, target) {
    var uploads = 0;	//Used for checking how many pictures are uploaded.
    target.append("<div id='uploader' style='width:100%;margin:10px; float:left;>" +
    "<p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>" +
    "</div>");

    $("#uploader").plupload({
        // General settings
        runtimes: 'html5,flash,silverlight,html4',
        url: 'upload.php',
        // User can upload no more then 500 files in one go (sets multiple_queues to false)
        max_file_count: 50,
        chunk_size: '0',
        filters: {
            // Maximum file size
            max_file_size: '5mb',
            // Specify what files to browse for.  We need more here?
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png,jpeg"},
            ]
        },
        // Rename files by clicking on their titles
        rename: false,
        // Sort files
        sortable: false,
        // Enable ability to drag'n'drop files onto the widget (currently only HTML5 supports that)
        dragdrop: true,
        // Views to activate
        views: {
            list: true,
            thumbs: true, // Show thumbs
            active: 'thumbs'
        },
        // Flash settings
        flash_swf_url: 'plupload/js/Moxie.swf',
        // Silverlight settings
        silverlight_xap_url: 'plupload/js/Moxie.xap',
        // PreInit events, bound before any internal events
        preinit: {
            Init: function (up, info) {
                // console.log('[Init]', 'Info:', info, 'Features:', up.features);
            },
            UploadFile: function (up, file) {

                // You can override settings before the file is uploaded
                // up.settings.url = 'upload.php?id=' + file.id;
                // up.settings.multipart_params = {param1 : 'value1', param2 : 'value2'};

                /**
                 * Denne funksjonen kj�res hver gang et bilde STARTER uploadingen.  F.eks. ved to bilder i bildek�en s� vil
                 * denne kj�res to ganger.  Da en gang for hver fil.
                 */

                up.settings.url = 'upload.php?fileName=' + file.name + "&url=" + file.id + "&imagesetId=" + imagesetId;
            }
        },
        // Post init events, bound after the internal events
        init: {
            Refresh: function (up) {
                // Called when upload shim is moved
                //  console.log('[Refresh]');

            },
            StateChanged: function (up) {
                // Called when the state of the queue is changed
                //   console.log('[StateChanged]', up.state == plupload.STARTED ? "STARTED" : "STOPPED");
                var pictureSet;

                //This happens right before the first picture is uploaded.
                if (up.state == plupload.STARTED && imagesetId == 0) {

                    //console.log("New imageset");
                    var name = $("#image-set-name").val();
                    var text = $("#image-set-description").val();

                    createNewPictureSet(name, text);
                }

                if (up.state == plupload.STOPPED) {	//This is called right after the last picture is uploaded.
                    if (imagesetId == 0) 	//when finishing a new set.
                    {
                        //console.log("Getting active id");
                        var id = getActiveImagesetId();
                        //console.log("Changing amount in set.");
                        changeAmountOfPicturesInSet(id, up.files.length);
                        var imageSet = new Array();
                        imageSet = getAllImagesInSet(id);
                        //console.log("Number of images in set: " + imageSet.length);

                        //console.log("Stop picture set");
                        stopPictureSet();

                        $.Notify({
                            style: {background: 'green', color: 'white'},
                            content: "Upload complete!"
                        });
                        setTimeout(function () {
                            $('#right-menu').empty();
                            setActive($(this));
                            enterName($('#right-panel'));
                            addOriginalButtonListener();
                        }, 1000);
                    }

                    else 					//When uploading to an existing pictureset
                    {
                        changeAmountOfPicturesInSet(imagesetId, up.files.length);

                        setTimeout(function () {
                            displayImageSet(imagesetId);
                        }, 4000);

                        /*var imageSet = new Array();			
                         imageSet = getAllImagesInSet(imagesetId);
                         showPictureSet(imageSet);*/
                        $.Notify({
                            style: {background: 'green', color: 'white'},
                            content: "Successfully added more images!"
                        });
                    }


                }
                //Hent siste bildesett som bruker opplastet. Det burde jo g� greit?
                //Ta s� bruker videre til en skjerm der han f�r sett alle bilder opplastet samt muligheten til � velge en original. 
                //Bilde nr 1 blir standard original om ikke noe annet blir valgt.  bruk radiobuttons
            },
            QueueChanged: function (up) {
                // Called when the files in queue are changed by adding/removing files
                //  console. log('[QueueChanged]');
            },
            UploadProgress: function (up, file) {
                // Called while a file is being uploaded
                //
                // console.log('[UploadProgress]', 'File:', file, "Total:", up.total);

            },
            FilesAdded: function (up, files) {
                // Callced when files are added to queue
                //console.log('[FilesAdded]');

                /* plupload.each(files, function(file) {
                 console.log('  File:', file);
                 });*/
            },
            FilesRemoved: function (up, files) {

                // Called when files where removed from queue
                /*console.log('[FilesRemoved]');

                 plupload.each(files, function(file) {
                 console.log('  File:', file);
                 });*/
            },
            FileUploaded: function (up, file, info) {

                //console.log('[FileUploaded] File:', file, "Info:", info, "Up:", up);
                var string = file.type;
                var string2 = file.name;

                var index = string.split("/");
                var fileType = index[index.length - 1];

                var index2 = string2.lastIndexOf(".");
                var nameOfFile = string2.substr(0, index2);

                var completeFileName = nameOfFile + "." + fileType;

                if (imagesetId == 0)	//When creating new imagesets.
                {
                    //console.log("Getting active id");
                    var id = getActiveImagesetId();
                    //console.log("Inserting image to database = " + id);
                    if (uploads == 0) {
                        console.log("hei")
                        insertImageToDatabase(completeFileName, id, file.id, 1);
                        uploads++;
                    } else {
                        var originalOrNot = getOriginal('#' + file.id);
                        console.log(originalOrNot);
                        //console.log(file.id + " har = " + originalOrNot);
                        insertImageToDatabase(completeFileName, id, file.id, originalOrNot);
                    }
                }
                else 				//When uploading to an existing imageset.
                {
                    //console.log("NOT getting active ID");
                    //console.log("Inserting image to database = " + imagesetId);

                    //If uploading an image to exisiting album it checks whether the "set as original" button is checked.
                    // if that is the case it sends a parameter with '1' to indicate that this picture is to be set as original.
                    var originalCheck = $('.image-data .input-control .radio').is(':checked');

                    if(originalCheck)    {
                        insertImageToDatabase(completeFileName, imagesetId, file.id, 1);
                    }
                    else    {
                        insertImageToDatabase(completeFileName, imagesetId, file.id, 0);    //Picture not to be set as original.
                    }
                }

            },
            ChunkUploaded: function (up, file, info) {
                // Called when a file chunk has finished uploading
                //  console.log('[ChunkUploaded] File:', file, "Info:", info);
            },
            Error: function (up, args) {
                // Called when a error has occured
                // console.log('[error] ', args);
            }
        }

    });


    // Handle the case when form was submitted before uploading has finished
    $('#form').submit(function (e) {
        // Files in queue upload them first
        if ($('#uploader').plupload('getFiles').length > 0) {

            // When all files are uploaded submit form
            $('#uploader').on('complete', function () {
                $('#form')[0].submit();
            });

            $('#uploader').plupload('start');
        } else {
            alert("You must have at least one file in the queue.");
        }
        return false; // Keep the form from submitting
    });
}
/**
 * Inserts a new picture into the database
 * with all its information.  Also returns the new ID for the picture to be stored as.
 */
function insertImageToDatabase(originalFileName, pictureSetId, url, isOriginal) {
    $.ajax
    ({
        url: 'ajax/scientist/insertPictureToDatabase.php',
        async: false,
        data: {
            'fileName': originalFileName,
            'pictureSetId': pictureSetId,
            'url': url,
            'isOriginal': isOriginal
        },
        success: function () {
            console.log("Inserted into database");
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}

function stopPictureSet() {
    $.ajax
    ({
        url: 'ajax/scientist/createNewImageSet.php',
        async: false,
        data: {
            'stopImageset': 'stopImageset'
        },
        success: function (data) {
            //console.log("Succesfully stopped imgset")
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}
/**
 * Creates a new imageset, and stores the new imageset Id in the session var.
 */
function createNewPictureSet(name, text) {
    $.ajax
    ({
        url: 'ajax/scientist/createNewImageSet.php',
        async: false,
        dataType: 'json',
        data: {
            'name': name,
            'text': text
        },
        success: function (data) {
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}

function alterImagesetName(imagesetId, name) {
    $.ajax
    ({
        url: 'ajax/scientist/alterImageSet.php',
        async: false,
        type: 'POST',
        data: {
            'option': 'updateName',
            'imagesetId': imagesetId,
            'name': name
        },
        dataType: 'json',
        success: function (data) {
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}

function alterImagesetText(imagesetId, text) {
    $.ajax
    ({
        url: 'ajax/scientist/alterImageSet.php',
        async: false,
        type: 'POST',
        data: {
            'option': 'updateText',
            'imagesetId': imagesetId,
            'text': text
        },
        dataType: 'json',
        success: function (data) {
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}

/**
 * This function will increment a imageset with amountToAdd.
 *  This could be both a negative and positive int.
 */
function changeAmountOfPicturesInSet(imagesetId, amountToAddOrRemove) {
    $.ajax
    ({
        url: 'ajax/scientist/alterImageSet.php',
        async: false,
        type: 'POST',
        data: {
            'option': 'updateAmountOfPictures',
            'imagesetId': imagesetId,
            'amount': amountToAddOrRemove
        },
        dataType: 'json',
        success: function (data) {
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}

function getActiveImagesetId() {
    var id;

    $.ajax
    ({
        url: 'ajax/scientist/alterImageSet.php',
        async: false,
        type: 'POST',
        data: {
            'option': "getActiveImagesetId"
        },
        dataType: 'json',
        success: function (data) {
            id = data;
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
    return id;
}

/**
 * Sets the original for a pictureset.
 * Will also check if there already exists an original.
 * This will then be set to 0, and the new one to 1
 */
function alterOriginalInSet(idOfOriginal) {
    $.ajax
    ({
        url: 'ajax/scientist/alterImageSet.php',
        async: false,
        type: 'POST',
        data: {
            'option': 'updateOriginal',
            'idOfOriginal': idOfOriginal
        },
        dataType: 'json',
        success: function (data) {
        },
        error: function (request, status, error) {
            console.log(request.responseText);
        }
    });
}
function checkRadioButtons() {
    //console.log("Checking radio buttons");
    var elements = document.getElementsByTagName("input");
    var checkedElements = 0;
    for (var i = 0; i < elements.length; i++) {	//SecurityCheck to make sure only 1 
        if (elements[i].checked == true)			//Radiobutton is checked.
            checkedElements++;
    }

    if (checkedElements == 1) {
        for (var i = 0; i < elements.length; i++) {	//SecurityCheck to make sure only 1 
            if (elements[i].checked == true) {			//Radiobutton is checked.
                alterOriginalInSet(elements[i].id);
                //console.log("Changed original!");
                target.append("Original set.");
                break;									//This shouldnt be necessary, but I put it there anyway.
            }
        }
    } else if (checkedElements == 0) {
        alert("You have to pick atleast one!");
    } else {
        alert("Something went wrong. Resetting radiobuttons now" +
        "<br/>Try again!");
        for (var i = 0; i < elements.length; i++) {	//SecurityCheck to make sure only 1 
            elements[i].checked = false;			//Radiobutton is checked.
        }
    }
}

/**
 * Adds "set original"-buttons next to each image and sets up listeners
 * @returns {undefined}
 */
function addOriginalButtonListener() {
    $(document.body).off('click', '.input-control.radio');
    $(document.body).on('click', '.input-control.radio', function () {
        $('.radio.original').prop('checked', false);
        $(this).children().children('input').prop('checked', true);
    });
}

/**
 * Returns true if image is marked as original
 * @param {type} $hash Must be a JQuery object in the format $('#hash')
 * @returns {unresolved} True if element is marked as original
 */
function getOriginal($hash) {
    console.log("getOriginal()");
    if ($($hash).children('div').children('div').children('label').children('input').prop('checked')) {
        return 1;
        console.log("original checked");
    } else {
        console.log("not original checked");

        return 0;
    }
}




















