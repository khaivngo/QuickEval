
/**
 * Sets up and manages image sets, and images and data within image sets. To be used in scientistPanel.php and sets  
 * up a listener on the "Manage Image Sets"-button on the left menu on load. 
 */

 $(document).ready(function() {
    $("#image-sets").click(function() {
        manageImageSets();
    });
});

/**
 * Sets up and displays image-sets on the div with id right-panel
 * @returns {undefined}
 */
 function manageImageSets() {
    inject("ajax/scientist/editImageSets.html");    //Injects framework
    setActive($(this));                             //Sets active on menu
    $(document.body).off();                         //Removes previous listeners
    $('#right-menu').load('ajax/scientist/imageSetMenu.html', function() {  //Adds menu with sort buttons
        $('#sort-alphabethical').addClass("active");        //Sets sort by alphabethical as default
        setUpMenuListeners();
    });
    if (setUpImageSetsGrid() != 0) {                
        setUpButtons();
    }

    setUpButtonListeners();
    $('.dropdown-menu').dropdown();
    toggleDisabled($('#delete-image-set'), 1);
}

/**
 * Sets the $target as active list item in the right menu
 * @param {type} $target
 * @returns {undefined}
 */
 function setActiveSort($target) {
    $target.siblings().removeClass('active');
    $target.addClass('active');
}

/**
 * Sets up listeners for sort-buttons
 * @returns {undefined}
 */
 function setUpMenuListeners() {
    $('#sort-alphabethical').click(function() {
        setActiveSort($(this));
        sortAlphabethical();
    });
    $('#sort-calendar').click(function() {
        setActiveSort($(this));
        sortCalendar();
    });
}

/**
 * Sets up listeners for tiles and buttons
 * @param {type} $images If images is true it will select images instead of image sets
 * @returns {undefined}
 */
 function setUpButtonListeners($images, $imageSet) {

    setUpTileListener($images);

    $(document.body).on('click', '#toggle-select', function() {
        ($images == true) ? toggleSelect(true) : toggleSelect();
    });

    $(document.body).on('click', '#delete-image-set', function() {

        if($('.tile.selected').length > 0) {
            if ($('#confirm-delete-div').length == 0) {
                deleteConfirm();
            } else {
                $('#confirm-delete-div').remove();
            }
        }        
    });

    $(document.body).on('click', '.notice', function() {
        $(this).remove();
    });

    if ($images == true) {
        $(document.body).on('click', '#confirm-delete', function() {

            if($('.tile.selected').length > 0) {
                deleteImages($imageSet);
            }
        });
        $(document.body).on('click', '#image-set-original', function() {
            setOriginal($imageSet);
        });
        $(document.body).on('click', '#image-set-submit', function() {
            submitImageSetChanges($imageSet);
        });
        $(document.body).on('click', '#add-image', function() {
            addImage($imageSet);
        });
        $(document.body).on('click', '#image-rename', function() {
            checkPictureName($('.tile.selected'), $imageSet);
        });
        $(document.body).on('click', '.picture-name', function() {
            $('.notice').remove();
        });


    } else {
        $(document.body).on('click', '#confirm-delete', function() {
            deleteImageSets($imageSet);
        });
    }

}

/**
 * Sets up tile listeners if images is true
 * @param {type} $images Boolean if used on images
 * @returns {undefined}
 */
 function setUpTileListener($images) {
    $(document.body).on('click', '.tile', function() {
        ($images == true) ? "" : displayImageSet($(this).parent().attr('imageset'));
    });
}

/**
 * Sorts the tiles based on image-set-name
 * @returns {undefined}
 */
 function sortAlphabethical() {
    setUpImageSetsGrid();
}

/**
 * Sorts the tiles based on id (date)
 * @returns {undefined}
 */
 function sortCalendar() {
    setUpImageSetsGrid(true);
}

/**
 * Sets up image sets grid with images 
 * @param {type} $sort Boolean, sorts by name if true
 * @returns {Boolean}
 */
 function setUpImageSetsGrid($sort) {


    var imageSets = getImageSets($sort);
    var tile; //Tile to display image set
    var row; //Row, contains tiles
    var grid = $('<div class="grid">'); //Sets up grid, contains rows
    var d; //Image set data
    var url; //image url
    var tiles = false; //Checks if there are tiles to be displayed

    for (var i = 0; i < imageSets.length; i++) {
        d = imageSets[i];
        url = 'uploads/' + d['person'] + '/' + d['id'] + '/' + d['url'] + '.' + d['pictureName'].substr(d['pictureName'].lastIndexOf('.') + 1);
        if (i % 2 == 0) {                           //If new row has to be created
            grid.append(row); //Adds last row to grid
            row = $('<div class="row">'); //Creates new row
        }
        tile = $('<div >'); //Sets up new tile



        tile.append(' <div class = "image-set" imageset="' + d['id'] + '" style = "margin: 0 10px">' +
                    '   <div class = "tile double sets">' +
                    '       <div class = "tile-content image">' +
                    '           <img class = "original-image" src = "' + url + '"/>' +
                    '       </div>' +
                    '       <div class = "brand bg-dark opacity">' +
                    '           <span class = "label fg-white image-set-name"> ' + d['name'] + ' </span>' +
                    '           <span class = "badge bg-orange image-count"> ' + d['pictureAmount'] + ' </span>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>'
                ); // Adds content to tile

        row.append(tile); //Adds image-set to row
        tiles = true;
    }

    grid.append(row); //Adds last row to grid

    $('.image-sets.grid').empty(); //Makes sure grid is empty before adding elements
    $('.image-sets.grid').append(grid).hide().fadeIn(); //Adds grid
    return tiles;
}


/**
 * Toggles selecting of tiles to be deleted.
 * @param {type} $images If true when managing images and not sets
 * @returns {undefined}
 */
 function toggleSelect($images) {
    toggleDisabled($('#delete-image-set'), 1);    //Displays/hides delete button

    if ($images) {                        //Displays "set original" button if managing images and not sets
        toggleDisabled($('#image-set-original'));
        toggleDisabled($('#image-rename'));
        toggleDisabled($('#picture-name-div'));
    }

    if ($('#toggle-select').hasClass('start')) {            //If user presses start selecting
        $(document.body).off('click', '.tile');             //Turn off listeners for tiles to navigate to imageset
        $(document.body).on('click', '.tile', function() {  //Turns on new listener if allows selecting
            $(this).toggleClass('selected');
            $('.notice').remove();
            return false;
        });
        $('#toggle-select').text('Stop Select');            //Changes text on button to "stop select"
        $('#toggle-select').removeClass('start');           //Removes "start" class to prepare for stop-select
        if ($images) {
            removeSlideshowListener();

        }
    } else {
        $(document.body).off('click', '.tile');                         //Removes listener for selecting
        if (!$images) {
            setUpTileListener();                                        //Adds listener for navigating to imageset
        } else {
            setUpTileListener(true);
        }
        $('.tile').removeClass('selected');                             //Unselects all tiles
        $('#confirm-delete-div').remove();                              //Removes the confirm delete buttons/text
        $('#toggle-select').text('Start Select');                       //Changes text to "start select"
        $('#toggle-select').addClass('start');                          //Prepares for start-select
        if ($images) {
            addSlideShowListener();
        }
    }
}

/**
 * TODO redo the appending buttons, should already be there.
 * Adds start select and delete image-set buttons
 * @returns {undefined}
 */
 function setUpButtons($images) {
    var html = '';
    if ($images) {
        html = html + '<br/><button id="add-image" class="image-button primary" style="width: 150px; height:26px; margin:10px; clear:both; float:left;">' +
        'Add Image <i class="icon-plus-2 bg-cyan on-right"/></button>';
    }

    html = html + '<br/><button id="toggle-select" class="button primary start sets"' +
    'style="width: 150px; margin:10px; clear:both; float:left;">Start Select</button><br/>' +
    '<button id="delete-image-set" class="image-button danger sets" style="width: 150px; margin:10px; clear:both; float:left;">' +
    'Delete Selected<i class="icon-remove bg-red"/></button>';

    if ($images) {
        html = html + '<br/><button id="image-set-original" class="button primary" style="width: 150px; margin:10px; clear:both; float:left;">' +
        'Set Original' +
        '</button>' +
        '<div id="picture-name-div" class="input-control text" data-role="input-control" style="width: 250px; margin:10px; clear:both; float:left; display:none">' +
        '   <input class="picture-name" type="text" placeholder="Input New Image Name">' +
        '   <button class="btn-clear" tabindex="-1"></button>' +
        '</div>' +
        '<br/><button id="image-rename" class="image-button primary" style="width: 150px; height:26px; margin:10px; clear:both; float:left;">' +
        'Rename Image<i class="icon-pencil bg-cyan"></i></button>';
    }

    $('#right-panel').append(html);
}

/**
 * Adds confirm-button and text after delete-button
 * @returns {undefined}
 */
 function deleteConfirm() {
    $('#delete-image-set').after(
                                 '<div id="confirm-delete-div" style="clear:both; float: left;">' +
                                 '<br/><span style="margin:10px" >Are you sure you want to delete these images?</span>' +
                                 '<br/><strong class="text-alert" style="margin:10px">NB: Will remove experiments where images are used!</strong>' +
                                 '<br/><button id="confirm-delete" class="button danger sets" style="margin:10px">Confirm Delete</button>' +
                                 '</div>');
    $('#add-image').goTo();
}

/**
 * Deletes all image-sets whose tiles are selected
 * @returns {undefined}
 */
 function deleteImageSets() {
    var imageSets = [];
    $('.tile.selected').parent().each(function() {      //Gets imageset-id from all selected tiles
        imageSets.push($(this).attr('imageset'));
    });
    deleteImagesetFromDatabase(imageSets);
    $.Notify({style: {background: 'green', color: 'white'},
             content: "Delete Successful!"
         });
    manageImageSets();
}

/**
 * Displays an image set on right-panel with images and input fields 
 * @param {type} $imageSet The id of the current imageset
 * @returns {undefined}
 */
 function displayImageSet($imageSet) {

    var data = getImageSetData($imageSet);
    var images = getAllImagesInSet($imageSet);
    console.log(images);
    $('#right-panel').load('ajax/scientist/viewImageSet.html', function() { //Loads imput fields, buttons
        $('#right-menu').empty();                                           // and hides buttons to be used later
        $('#image-set-title').text(data['name']);           //Fills title, name and description
        $('#image-set-name').val(data['name']);
        $('#image-set-description').val(data['text']);
        $(document.body).off();                             //Removes active listeners

        displayAllImages(images);                           //Displays all images

        setUpButtonListeners(true, $imageSet);              //Sets up button-listeners
        setUpButtons(true);                         
        toggleDisabled($('#delete-image-set'), 1);          //Hides buttons/input fields for deleting/original/renaming
        toggleDisabled($('#image-set-original'));
        toggleDisabled($('#image-rename'));
        toggleDisabled($('#picture-name-div'));
    });
}

/**
 * Gets name and description of imageset
 * @param {type} $imageSet ID of image set
 * @returns {data}
 */
 function getImageSetData($imageSet) {
    var imageSet;
    $.ajax
    ({
        url: 'ajax/scientist/getImageSet.php',
        data: {'imageSet': $imageSet},
        type: 'post',
        async: false,
        dataType: 'json',
        success: function(data)
        { 
            imageSet = data;
        },
        error: function(request, status, error) {
            console.log(request.responseText);
        }
    });
    return imageSet;
}

/**
 * Displays images in a grid using tiles. Original will be displayed first with a cyan-colored brand
 * @param {type} $images Array of images with data and url
 * @returns {undefined}
 */
 function displayAllImages($images) {
    for (var i = 0; i < $images.length; i++) {
        var html = '<div class = "image-set" imageid="' + $images[i]['id'] + '" style = "margin: 0 10px">' +
        '   <div class = "tile double sets">' +
        '       <div class = "tile-content image">' +
        '           <a href="' + $images[i]['url'] + '" class="highslide" title="' + $images[i]['name'] + '" onclick="return hs.expand(this)">' +
        '               <img class = "original-image" src = "' + $images[i]['url'] + '"/>' +
        '           </a>' +
        '       </div>';
        if (i == 0) {
            html = html + '<div class = "brand bg-cyan opalocicty">';
        } else {
            html = html + '<div class = "brand bg-dark opalocicty">';
        }

        html = html + '<span class = "label fg-white image-set-name"> ' + $images[i]['name'] + ' </span>' +
        '       </div>' +
        '   </div>' +
        '</div>';

        $('#image-set-images').append(html);
    }
}

/**
 * Removes listener for slideshow on tiles
 * @returns {undefined}
 */
 function removeSlideshowListener() {
    $('.highslide').removeAttr('onclick');
}

/**
 * Adds listener for slideshow on tiles
 * @returns {undefined}
 */
 function addSlideShowListener() {
    $('.highslide').attr('onclick', 'return hs.expand(this)');
}

/**
 * Checks values of name and description and updates these values in database
 * @returns {undefined}
 */
 function submitImageSetChanges($imageSet) {
    var name = $('#image-set-name').val();
    var desc = $('#image-set-description').val();
    $('.notice').remove();

    if (name.length > 2) {  //Checks name length
        alterImagesetName($imageSet, name);
        alterImagesetText($imageSet, desc);

    } else {                //Notice if name is too short
        $('#image-set-name').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                                   'Name needs to be 3 characters or longer!' +
                                   '</div>');
    }
}

/**
 * Deletes all selected images
 * @param {type} $imageSet ID of image set
 * @returns {undefined}
 */
 function deleteImages($imageSet) {
    var images = [];

    if ($('.tile').first().is($('.tile.selected'))) {           //Notice if trying to delete original
        $.Notify({style: {background: 'red', color: 'white'},
                 content: "Delete Failed!"
             });
        $('#confirm-delete').after('<div id="notify" class="bg-red notice marker-on-top span1">' +
                                   'You cannot delete the original!' +
                                   '</div>');
    } else {

        $('.notice').remove();
        $('.tile.selected').parent().each(function() {      //Gets imageset-id from all selected tiles
            images.push($(this).attr('imageid'));
        });

        deleteImageFromDatabase(images);                    //Removes images
        $('.tile.selected').parent().remove();
        $.Notify({style: {background: 'green', color: 'white'},
                 content: "Delete Successful!"
             });
        displayImageSet($imageSet);                         //Updates with existing images
    }
}

/**
 * Set selected image as original in image set
 * @param {type} $imageSet ID of image set
 * @returns {undefined}
 */
 function setOriginal($imageSet) {
    var images = [];

    $('.notice').remove();
    $('.tile.selected').parent().each(function() {      //Gets imageset-id from all selected tiles
        images.push($(this).attr('imageid'));
    });
    if (images.length != 1) {           //If none or more than one image is selcted
        $('.picture-name').before('<div id="notify" class="bg-red notice marker-on-top span1" style="z-index: 10; margin:10px">' +
                                  'Invalid amount of images selected' +
                                  '</div>');
    } else {                            //If one image is selected
        alterOriginalInSet(images[0]);  //Sets original
        $.Notify({style: {background: 'green', color: 'white'},
                 content: "Original set successfully"
             });
        displayImageSet($imageSet);     //Updates images
    }
}

/**
 * Deletes images from database
 * @param imageid which contains an array with image ID's
 */
 function deleteImageFromDatabase(imageIds) {
    $.ajax
    ({
        url: 'ajax/admin/deleteImages.php',
        async: false,
        data: {
            'option': "images",
            'imageArray': imageIds
        },
        dataType: 'json',
        success: function(data) {
                    console.log("Deleted all images!= " + data);	//FJERN
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                }
            });
}

/**
 * Deletes entire image set from database
 * @param imageset Id which contains an array with imagesets Ids
 */
 function deleteImagesetFromDatabase(imagesetIds) {
    $.ajax
    ({
        url: 'ajax/admin/deleteImages.php',
        async: false,
        data: {
            'option': "imageset",
            'imageArray': imagesetIds
        },
        dataType: 'json',
        success: function(data) {
                    console.log("Deleted imageset! = " + data);	//FJERN
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                }
            });
}

/**
 * Displays a uploader to add images to imageset
 * @param {type} $imageSet
 * @returns {undefined}
 */
 function addImage($imageSet) {
    if ($('.upload-image-to-set').length > 0) {
        $('.upload-image-to-set').remove();
    } else {
        div = $('<div class="upload-image-to-set" style="clear:both; float: left;">');
        $('#add-image').after(div);
        startUploader($imageSet, div);
    }
}

/**
 * Checks length of input name.
 * @param {type} $imageSet Id og image set which picture resides in.
 * @returns {undefined}
 */
 function checkPictureName($pictureId, $imageSet) {
    var name = $('.picture-name').val()
    if ($pictureId.length != 1) {       //If none or more than one image is selected
        $('.picture-name').after('<div id="notify" class="bg-red notice marker-on-top span1" style="z-index: 10; margin:10px">' +
                                 'More than one image selected!' +
                                 '</div>');
    } else {                            //If one image is selected
        if (name.length > 2) {          //If length of name is 3 characters or longer
            updatePictureName($pictureId.first().parent().attr('imageid'), name);
            displayImageSet($imageSet);
        } else {
            $('.picture-name').after('<div id="notify" class="bg-red notice marker-on-top span1" style="z-index: 10; margin:10px">' +
                                     'Name needs to be 3 characters or longer!' +
                                     '</div>');
        }
    }
}

/**
 * Updates name of picture
 * @param {type} $imageSet Id of image set picture resides in
 * @returns {undefined}
 */
 function updatePictureName($pictureId, $name) {

    $.ajax({
        type: 'POST',
        url: 'ajax/scientist/updatePictureName.php',
        data: {'pictureId': $pictureId,
        'name': $name},
        success: function(data) {
            $.Notify({//notifies user about successfull email change
                content: "Picture name updated",
                style: {background: 'lime'},
            });
        },
        error: function(request, status, error) {
            console.log(request.responseText);
        }
    });
}

function toggleDisabled($el, $danger) {

    $el.toggleClass(($danger == 1) ? 'danger' : 'primary').toggleClass('fg-gray');  //Toggles disabled style on buttons
    $el.children('i').toggleClass(($danger == 1) ? 'bg-red' : 'bg-cyan' );          //Toggles disabled style on icons within buttons
    $el.prop('disabled', !$el.prop('disabled'));                                    //Toggles disabled buttons
    
}