var loaded; //Sets to one if page is loaded, used in stepper, as stepper "clicks" last element.
// Has to be global.
var imagePairCheck; //0 until user presses finish on setupexperiment with a custom queue, and all images aren't matched
jQuery.fn.swap = function(b) {
    // method from: http://blog.pengoworks.com/index.cfm/2008/9/24/A-quick-and-dirty-swap-method-for-jQuery
    b = jQuery(b)[0];
    var a = this[0];
    var t = a.parentNode.insertBefore(document.createTextNode(''), a);
    b.parentNode.insertBefore(a, b);
    t.parentNode.insertBefore(b, t);
    t.parentNode.removeChild(t);
    return this;
}
$(document).ready(function() {

    //Adds listener to menu
    $("#set-up-experiment").click(function() {
        $.ajax({
            async: false,
            url: "ajax/scientist/setupExperiment.html",
            success: function(data) {
                $($('#right-panel')).html(data);
                $('#right-menu').empty();
                setUpDefaultInputs();
                $('.hint-trigger').hint(); //Sets up hints as DOM is loaded
            },
            error: function(request, status, error) {
                alert(request.responseText);
            }
        });

        setUpExperimentTypes();

        loaded = 0; //Resets loaded variable
        imagePairCheck = 0;
        setUpStepperListener();
        setActive($(this)); //Sets up stepper
        $('#stepper').stepper({
            onStep: function(index, step) {
                var check = 1;
                if (index == 2) { //When steps to 2, check step 1 values
                    if (!step1Check()) {
                        check = 0;
                        $('#stepper').stepper('prior');
                    }
                }
                if (index == 5) {
                    if (!step4Check()) {
                        check = 0;
                        $('#stepper').stepper('prior');
                    }
                }
                if (check) {
                    $('[id^=ex-step]').hide();
                    $('#ex-step' + index).fadeIn();
                    setUpFinishButton(index);
                }
            },
            steps: 5,
            start: 1
        });
        $(document.body).on('click', '.notice', function() {
            $(this).parent().remove();
        });
        //---------------- STEP 1 --------------------//
        $('#ex-optional-fields').hide(); //Hides step 1 optional fields
        $('#ex-description-up').hide(); //Hides the visual "down"-icon
        $('#ex-open-optional-fields').click(function(event) {
            event.preventDefault(); //Prevents page jumping to top
            $('#ex-optional-fields').slideToggle(); //Toggles the optional fields
            $('#ex-description-up').toggle(); //Changes direction on the up/down-icons
            $('#ex-description-down').toggle();
        });

        //$('#algorithm-type').append('<option>' + 'Custom Queue' + '</option>');
        algorithmSelect(); //Updates step 3 with disabled buttons
        $('#algorithm-type').prop('disabled', true); //Disabless algorithm type before method is picked
        $('#algorithm-type').append('<option>Random Queue</option>');

        //Listener for selecting experimenttype
        $(document.body).off('change', '#experiment-type');
        $(document.body).on('change', '#experiment-type', function() {
            populateExperimentSelect();
        });

        //Removes notice when adding name
        $(document.body).off('keyup', '#ex-name');
        $(document.body).on('keyup', '#ex-name', function() {
            $('.notice').parent().remove();
        });

        //Removes notice when adding description
        $(document.body).off('keyup', '#ex-long-description');
        $(document.body).on('keyup', '#ex-long-description', function() {
            $('.notice').parent().remove();
        });

        //Listener for changing algorithm
        $(document.body).off('change', '#algorithm-type');
        $(document.body).on('change', '#algorithm-type', function() {
            algorithmSelect();
        });
        //---------------- STEP 3 --------------------//
        $('#ex-add-field').click(function() { //Displays an additional field for the scientist to use
            $('.notice').parent().remove();
            customFieldAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addCustomField.html');
            $(this).before(div);
            return null;
        });
        $('#ex-add-field-history').click(function() { //Displays dropdown menu from previously used fields for
            customFieldAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addCustomFieldFromHistory.html', function() {
                //Fills dropdown with values from database
                getInputfields().forEach(function(t) {
                    div.find('select').append('<option infotype=' + t['id'] + '>' + t['info'] + '</option>');
                });
            });
            $('#ex-add-field').before(div); // scientist to use
            return null;
        });
        //---------------- STEP 4 --------------------//
        //Listener for remove button used on elements with remove(thrashcan) button, removes parent div
        $(document.body).off('click', '.remove-parent');
        $(document.body).on('click', '.remove-parent', function(e) {
            e.preventDefault();
            $(this).parent().parent().parent().remove();
        });
        $('#ex-add-image-set').click(function() { //Adds a dropdown menu with image sets
            experimentQueueAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/imageSetDropDown.html', function() {
                $('#ex-add-instruction').before(div);
                $('.dropdown-menu').dropdown();
                var currentSet = $('.ex-image-set-inner').last();
                var width = $('.ex-img').outerWidth(true) * (currentSet.children().length + 1);
                $('.ex-image-set-inner').css("width", width);
                $('.ex-image-order').hide();
                div.find('select').append('<option disabled selected>Select Image Set</option>'); //Adds default option
                getImageSets().forEach(function(t) {
                    div.find('select').append('<option image-set=' + t['id'] + '>' + t['name'] + '</option>'); //Adds imagesets as options
                    div.find('select').unbind('change'); //Fixes double change call
                    div.find('select').change(function() { //Sets up change handler for select
                        var imageset = $(this).find('option:selected:not(:disabled)').attr('image-set');
                        var images = getAllImagesInSet(imageset);
                        $(this).closest('.image-set').find('.fullscreen-image').attr({
                            href: images[0]['url'],
                            target: '_blank'
                        });
                        imageSetChange($(this));
                    }).focus(function() { //Sets firstSelect to true if last selected value was default option
                        if ($(this).find('option:selected:disabled').length == 1) {
                            $(this).data('firstSelect', true);
                        }
                    });
                });
                setUpDragNDrop(); //Adds drag and drop functionality
            });
            return null;
        });
        $('#ex-add-instruction').click(function() { //Listener for add instruction
            customFieldAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addInstructionField.html', function() {
                setUpDragNDrop();
            });
            $('#ex-add-instruction').before(div);
            setUpDragNDrop();
            return null;
        });
        $('#ex-add-instruction-history').click(function() { //Listener for add existing instruction
            experimentQueueAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addInstructionFromHistory.html', function() {
                setUpDragNDrop();
                getInstructions().forEach(function(t) {
                    div.find('select').append('<option instruction="' + t['id'] + '">' + t['text'] + '</option>');
                });
            });
            $('#ex-add-instruction').before(div);
            setUpDragNDrop();
            return null;
        });
        $(document.body).off('click', '.remove-image-set'); //Removes existing listener
        $(document.body).on('click', '.remove-image-set', function() { //Adds listener for remove image-set button
            var activeImageSet = $(this).closest('.image-set-div').parent();
            var imageSets = $('select > option:selected:not(:disabled)').closest('.image-set-div').parent();
            if (imageSets.length != 0) { //There are queues to be removed as well
                var index = imageSets.index(activeImageSet);
                $('.ex-image-pair').eq(index).remove(); //Removes the image-set queue on step 4
            }
            activeImageSet.remove(); //Removes imageset
        });
        /---------------- STEP 5 --------------------/ / $(document.body).off('click', '.finish');
        $(document.body).on('click', '.finish', function() {
            finishExperiment();
        });
        $(document.body).off('click', '.notice.image-queue-check');
        $(document.body).on('click', '.notice.image-queue-check', function() {
            $(this).parent().remove();
            checkMatchedPairs();
        });
        $('#ex-add-category').click(function() { //Listener for add instruction
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addCategory.html', function() {
                setUpDragNDrop();
            });
            $('#ex-add-category').parent().before(div);
            setUpDragNDrop();
            return null;
        });
        $('#ex-add-category-history').click(function() { //Listener for add existing instruction
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addCategoryFromHistory.html', function() {
                setUpDragNDrop();
                getCategories().forEach(function(t) {
                    div.find('select').append('<option category="' + t['id'] + '">' + t['name'] + '</option>');
                });
            });
            $('#ex-add-category').parent().before(div);
            setUpDragNDrop();
            return null;
        });
    });
});

/**
 * Makes sure all fields in step 1 is filled. Also adds notify for user to fill
 * @return {int} 1 if passed, 0 if not
 */
function step1Check() {
    check = 1;
    $('.notice').parent().remove();
    if ($('#ex-name').val() == "") { //Check experimentname
        check = 0;
        $('#ex-name').after('<div id="notify"><div class="span3" style="margin: 20px 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'Name of experiment is required' + '</div></div>');
    }
    if ($('#ex-long-description').val() == "") { //Check experiment description
        check = 0;
        $('#ex-long-description').after('<div id="notify"><div class="span3" style="margin: 20px 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'Description is required' + '</div></div>');
    }
    if ($('#algorithm-type').val() == null) { //Check algorithm dropdown
        check = 0;
        $('#algorithm-type').after('<div id="notify"><div class="span3" style="margin: 20px 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'Algorithm for images required' + '</div></div>');
    }
    return check;
}

/**
 * Makes sure there is at least one image set on step 4. Also adds notify for user to add one
 * @return {int} 1 if passed, 0 if not
 */
function step4Check() {
    check = 1;
    $('.notice').parent().remove();
    if ($('select > option:selected:not(:disabled)').closest('.image-set-div').parent().length == 0) {
        check = 0;
        $('#ex-add-instruction-history').after('<div id="notify"><div class="span3" style="margin: 20px 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'At least one imageset is required' + '</div></div>');
    }
    return check;
}


function pictureOrder($element) {
    var images = $element.siblings('div').children('div').children('div').children('img');
    currentSet = images.parent().parent();
    if (images.hasClass('span1')) {
        images.removeClass('span1');
        images.addClass('span2');
        var width = $('.ex-img').outerWidth(true) * (currentSet.children().length + 1);
        $('.ex-image-set-inner').css("width", width);
        $('.ex-image-order').slideToggle();
    } else {
        images.removeClass('span2');
        images.addClass('span1');
        var width = $('.ex-img').outerWidth(true) * (currentSet.children().length + 1);
        $('.ex-image-set-inner').css("width", width);
        $('.ex-image-order').slideToggle();
    }
}

/**
 * Removes step 5
 */
function removeLastStep() {
    $('.stepper > ul > li').eq(4).hide();
    $('.stepper > ul').css('width', '75%');
}

/**
 * Displays step 5
 */
function addLastStep() {
    $('.stepper > ul > li').eq(4).fadeIn();
    $('.stepper > ul').css('width', '100%');
}

/**
 * Fills dropdownmenu with available experiment types
 */
function setUpExperimentTypes() {
    var data = loadExperimentTypes();
    $('#experiment-type').append('<option value="" disabled selected>Select experiment type</option>');
    for (var i = 0; i < data.length; i++) { //iterates through data and appends option elements to select.
        $('#experiment-type').append('<option>' + data[i]['name'] + '</option>');
    }
}

/**
 * Gets available algorithms for pictures and fills different arrays.
 * @returns {undefined}
 */
function loadAlgorithmTypes() {
    $.ajax({
        type: 'POST',
        url: 'ajax/scientist/getAlgorithmTypes.php',
        data: {},
        dataType: 'json',
        success: function(data) {
            for (var i = 0; i < data.length; i++) { //iterates through data and appends option elements to select.
                $('#experiment-type').append('<option>' + data[i]['name'] + '</option>');
            }
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Gets experiment methods and algorithms, removes step 5 based on choices.
 */
function populateExperimentSelect() {
    var pairing = new Array('Random Queue', 'Custom Queue'); //Algorithms on pair-comparison
    var other = new Array('Random Queue'); //Algorithms on other methods
    var method = $('#experiment-type').val(); //Current method
    //Adds the right options and parameters based on method
    displayParameters(method);
    setUpStep5(method);
    //Resets checkboxes for new method
    $('#ex-step2').find('input[type=checkbox]').attr('checked', false);
    if (method == 'Rank order') {
        $('#algorithm-type').empty();
        other.forEach(function(t) {
            $('#algorithm-type').append('<option>' + t + '</option>');
        });
        $('#algorithm-type').prop('disabled', true);
        removeLastStep();
    }
    if (method == 'Paired comparison') {
        $('#algorithm-type').empty();
        pairing.forEach(function(t) {
            $('#algorithm-type').append('<option>' + t + '</option>');
        });
        $('#algorithm-type').prop('disabled', false);
        removeLastStep();
    }
    if (method == 'Category judgement') {
        $('#algorithm-type').empty();
        other.forEach(function(t) {
            $('#algorithm-type').append('<option>' + t + '</option>');
        });
        $('#algorithm-type').prop('disabled', true);
        addLastStep();
    }
    if (method == 'Artifact marking') {
        $('#algorithm-type').empty();
        other.forEach(function(t) {
            $('#algorithm-type').append('<option>' + t + '</option>');
        });
        $('#algorithm-type').prop('disabled', true);
        removeLastStep();
    }
    if (method == 'Triplet comparison') {
        $('#algorithm-type').empty();
        other.forEach(function(t) {
            $('#algorithm-type').append('<option>' + t + '</option>');
        });
        $('#algorithm-type').prop('disabled', true);
        addLastStep();

        // add text explaining how many images is allowed in a triplet comparison image set
        // $('#ex-step4 form fieldset legend').after(
        //     'This experiment only works with image sets containing<br> 7, 9, 13, 15, 19, 21, 25 or 27 images (excluding the original image).'
        // );
    }

    /**
     * Displays pair parameters based on type
     * @param  {string} type type of experiment
     */
    function displayParameters(type) {
        type == "Paired comparison" ? $('#forced-pick').show() : $('#forced-pick').hide();
        type == "Paired comparison" ? $('#same-pair').show() : $('#same-pair').hide();

        type == "Triplet comparison" ? $('#forced-pick').show() : $('#forced-pick').hide();
        // type == "Triplet comparison" ? $('#same-pair').show() : $('#same-pair').hide()
    }

    /**
     * Displays categories or queues in step 5 based on type
     * @param {string} type type of experiment
     */
    function setUpStep5(type) {
        if (type == "Category judgement") {
            $('#ex-image-queues').hide();
            $('#ex-setup-categories').show();
        } else if (type == "Triplet comparison") {
            $('#ex-image-queues').hide();
            $('#ex-setup-categories').show();
            $('#ex-setup-categories legend').after(
                'The ISO standard recommends 3, 5 or 7 categories ' +
                '(ex: favorable, acceptable, just acceptable, unacceptable, poor).'
            );
        } else {
            $('#ex-image-queues').show();
            $('#ex-setup-categories').hide()
        }
    }
    $('#ex-step4').find('div').children('div').remove();
    $('.ex-image-pair').remove();
}

/**
 * Removes steps based on experimenttype
 * @return {[type]} [description]
 */
function algorithmSelect() {
    if ($('#algorithm-type').val() == "Custom Queue") {
        $('#ex-step3').find('button').prop('disabled', 'false');
        addLastStep();
    } else {
        removeLastStep();
    }
    $('#ex-step4').find('div').children('div').remove();
    $('.ex-image-pair').remove();
    $('.notice').parent().remove();
}

/**
 * Receives data and sends data to php script which saves data about experiment to database.
 * @param {type} $name states names of experiment
 * @param {type} $sDesc a short description of experiment
 * @param {type} $lDesc a longer desriction of experiment
 * @param {type} $eType the type of experiment this is
 * @param {type} $algo chosen algorithm for the pictures
 * @param {type} $sWhiteP screen whitepoint
 * @param {type} $sLum scrren lumineance, measured in cd/m2
 * @param {type} $wRoomI whitepoint room illumination
 * @param {type} $aIllum ambient illumination, measured in lux
 * @returns {undefined}
 */
function insertExperimentInformation($name, $sDesc, $lDesc, $eType, $algo, $sWhiteP, $sLum, $wRoomI, $aIllum) {
    $.ajax({
        type: 'POST',
        url: 'ajax/scientist/insertExperimentInformation.php',
        data: {
            'name': $name,
            'sDesc': $sDesc,
            'lDesc': $lDesc,
            'name': $eType,
            'algo': $algo,
            'sWhiteP': $sWhiteP,
            'sLum': $sLum,
            'wRoomI': $wRoomI,
            'aIllum': $aIllum
        },
        dataType: 'json',
        success: function(data) {},
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
}

/**
 * Sets up listener for level-buttons on stepper. Skips first click using the loaded variable, as the stepper
 * clicks once on last step as its loaded
 * @returns {undefined}
 */
function setUpStepperListener() {
    $(document.body).off('click', 'li'); //Makes sure only one listener for stepper is active at a time
    $(document.body).on('click', 'li', function() { //Sets up listener for stepper to enable navigating by clicking
        if (loaded == 1) { // steps
            var diff = ($(this).index() - $('.current').index()); //Gets amount of steps from current to selected
            if (diff == 1) { //Adds step if differance was 1
                $('#stepper').stepper('next'); //Only 1 step forward is allowed at a time
            } else if (diff < 0) { //Removes steps if differance was negative
                for (var i = 0; i > diff; i--) {
                    $('#stepper').stepper('prior');
                }
            }
        }
        loaded = 1; //Updates loaded
    });
}

/**
 * Swaps the position of two elements
 * @param {type} a The first element
 * @param {type} b The second element
 * @returns {undefined}
 */
function swapElements(a, b) {
    aP = a.parent(); //Gets the elements parent
    bP = b.parent();
    aP.append(b); //Appends the new elements
    bP.append(a);
    a.css('top', 0); //Prevents weird positioning after dropping
    a.css('left', 0);
}

/**
 * Enables dragging and dropping on elements with classes draggable and droppable.
 * Dropping one element on another switches their position via the swapElements function
 * @param {type} bool Set to true if managing image-queues and not experimentqueue
 * @returns {undefined}
 */
function setUpDragNDrop(bool) {
    //Adds dragging
    $('.draggable').draggable({
        revert: "invalid", //Return element to its position if drop on non-dropzone
        stack: "div", //Dragged elements are always on top
        containment: '.steps',
        //Used on picture queue. Adds droppable only when dragging is started, to contain droppable zones only to
        // elements within the same picture set
        start: function(event, ui) {
            if (bool == true) {
                $(this).parent().siblings().children('.input-control').droppable({
                    drop: function(event, ui) {
                        swapElements(ui.draggable, $(this)); //Swaps the elements
                        $('.ex-pair.ui-droppable').droppable('destroy');
                    }
                });
            }
        },
        //Used on picture queue. Removes droppable functionality when dragging stops, to prevent enabling dragging between
        // picture-sets
        stop: function(event, ui) {
            if (bool == true) {
                $('.ex-pair.ui-droppable').droppable('destroy');
            }
        }
    });
    //Sets all elements with class droppable. Used on experimentqueue only
    if (bool != true) {
        //Adds dropping
        $('.droppable').droppable({
            drop: function(event, ui) {
                if (checkDragNDropElements($(this), ui.draggable)) {
                    swapQueue(ui.draggable, $(this));
                } else {
                    checkDragNDropOtherElements($(this), ui.draggable); //Checks if user swaps a imageset with nonimageset
                }
                swapElements(ui.draggable, $(this)); //Swaps the elements
            }
        });
    }
}
/**
 * Adds listener to add-pair button to add a new pair including listener for buttons within pair.
 * @returns {undefined}
 */
function setUpAddPairListener() {
    //Adds onclick listener to add-pair buttons
    $(document.body).off('click', '.add-pair');
    $(document.body).on('click', '.add-pair', function() {
        $('.notice').parent().remove();
        var d = $('<div class="pair-div"></div>');
        var t = $(this);
        d.load('ajax/scientist/setupexperiment/addCustomPair.html', function() { //Loads html for new pair
            var index = d.closest('.ex-image-pair').index();
            var id = $('select > option:selected:not(:disabled)').closest('.image-set-div').parent().eq(index).find('option:selected').attr('image-set');
            getAllImagesInSet(id).forEach(function(t) {
                d.find('.first-image').append('<option imageid=' + t['id'] + '>' + t['name'] + '</option>');
                d.find('.second-image').append('<option imageid=' + t['id'] + '>' + t['name'] + '</option>');
            });
            setUpRemovePairListener();
            setUpDragNDrop(true);
        });
        $(this).before(d); //Adds loaded pair
        $(this).parent().siblings('.existing-queue').children('select').hide(); //Hides select and
        $(this).parent().siblings('.input-control.text').fadeIn(); // displays inputfield
    });
}
/**
 * Adds listener to remove-pair listener. Also handles select/input-fields when there are no pairs left
 * @returns {undefined}
 */
function setUpRemovePairListener() {
    //Adds onclick listener to remove-pair buttons
    $(document.body).off('click', '.remove-pair');
    $(document.body).on('click', '.remove-pair', function() {
        var imageQueue = $(this).closest('.image-set-queue'); //Gets root element
        if ($(this).parent().parent().siblings().length == 1) { //If there is only one sibling - this element
            imageQueue.find('.queue-name').parent().hide(); // which will later be removed, hides name and
            // displays select
            imageQueue.find('.existing-queue').children('select').fadeIn();
        }
        $(this).closest('div > .ex-pair').parent().remove();
    });
}
/**
 * Gets index of two elements and swaps them through swapWith function
 * @param {type} a first element to be swapped
 * @param {type} b second element to be swapped
 * @returns {undefined}
 */
function swapQueue($a, $b) {
    //alert($(a).children('.image-set').parent().index());
    var aPos = $($a).index('.image-set-div');
    var bPos = $($b).index('.image-set-div');
    $('.ex-image-pair').eq(aPos).swapWith($('.ex-image-pair').eq(bPos));
}
/**
 * Swaps the elements on dom level
 * @author Paolo Bergantino http://stackoverflow.com/questions/698301/is-there-a-native-jquery-function-to-switch-elements
 * @param {type} to
 * @returns {jQuery.fn@call;each}
 */
jQuery.fn.swapWith = function($to) {
    return this.each(function() {
        var copy_to = $($to).clone(true);
        var copy_from = $(this).clone(true);
        $($to).replaceWith(copy_from);
        $(this).replaceWith(copy_to);
    });
};
/**
 * Manages imagesets and its queue on step 5. Called when user changes the dropdown selecting imageset name
 * on step 4. Adds or edits the queue based on imagename selected and if its first select from the
 * default value "Select image set", or an edit
 * @param {type} $a the dropdown-menu to be changed
 * @returns {undefined}
 */
function imageSetChange($a) {
    var index = getImageSetIndex($a); //Saves index of imageset of active imagesets
    var div = $('<div class="ex-image-pair"></div>'); //Creates div to contain picture set
    var id = $a.find('option:selected:not(:disabled)').attr('image-set');
    if ($('#algorithm-type').val() == "Custom Queue") {
        loadImageSet(div, $a.val(), id); //Loads picture set into div
        if ($a.data('firstSelect') == false) { //Image set existed and was changed
            $('.ex-image-pair').eq(index).after(div); //Adds new imageset to queue
            $('.ex-image-pair').eq(index).remove(); //Removes existing imageset
        } else {
            //Create new image set
            if ($('.ex-image-pair').length == 0) { //If there are no active imagesets
                $('#ex-image-sets').append(div); //Adds loaded picture set html
            } else if (index == 0) {
                $('#ex-image-sets').prepend(div);
            } else { //If there are existing queues
                $('.ex-image-pair').eq(index - 1).after(div); //Adds queue to its right position
            }
        }
    }
    $a.data('firstSelect', false); //Sets firstselect to false as user has selected an imageset
}
/**
 * Loads an imageset into element $a
 * @param {type} $a element for imageset to be loaded into
 * @param {type} name name of imageset
 * @returns {undefined}
 */
function loadImageSet($a, $name, $id) {
    $a.load('ajax/scientist/setupexperiment/addImageSetQueue.html', function() {
        setUpAddPairListener();
        var pictureQueues = getEarlierPictureQueues($id);
        if (pictureQueues.length > 0) {
            pictureQueues.forEach(function(t) {
                $a.find('select').append('<option imagequeue="' + t['id'] + '">' + t['title'] + '</option>');
            });
        }
        $(this).find('.image-set-name').append($name); //Adds the picture set name
        $('.queue-name').parent().hide(); //Hides the queue name
        $(this).find('.queue-name').keyup(function() {
            $('.notice').parent().remove();
        });
    });
}
/**
 * Gets imagesetindex of element $a for imagesets who are initialized and does not have name "Select Image Set"
 * @param {type} $a any element within imageset
 * @returns {unresolved} Index for imageset
 */
function getImageSetIndex($a) {
    var activeImageSet = $a.closest('.image-set-div').parent();
    var imageSets = $('select > option:selected:not(:disabled)').closest('.image-set-div').parent();
    return imageSets.index(activeImageSet);
}
/**
 * Checks to elemnents if they are both active imagesets
 * @param {type} a first element to be matched
 * @param {type} b second element to be matched
 * @returns {Boolean} true if elements are both active imagesets
 */
function checkDragNDropElements($a, $b) {
    if ($a.children().hasClass('image-set') && $b.children().hasClass('image-set')) {
        if ($a.find('option:selected:not(:disabled)').length == 1 && $b.find('option:selected:not(:disabled)').length == 1) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
/**
 * Checks for one element which is active imageset and one which is not, instruction or inactive, and fixes queue based on
 * distance between elements via the swapImageSetAndElement function
 * @param {type} $a first element
 * @param {type} $b second element
 * @returns {undefined}
 */
function checkDragNDropOtherElements($a, $b) {
    aImageSet = $a.children().hasClass('image-set');
    aSelected = $a.find('option:selected:not(:disabled)');
    bImageSet = $b.children().hasClass('image-set');
    bSelected = $b.find('option:selected:not(:disabled)');
    if ((aImageSet == true && aSelected.length == 1) && (bSelected.length == 0)) {
        swapImageSetAndElement($a, $b);
    } else if ((bImageSet == true && bSelected.length == 1) && (aSelected.length == 0)) {
        swapImageSetAndElement($b, $a);
    }
}
/**
 * Fixes queue when imageset and non imageset is switched
 * @param {type} $a active imageset
 * @param {type} $b non imageset, either instruction or inactive imageset
 * @returns {undefined}
 */
function swapImageSetAndElement($a, $b) {
    var activeImageSet = $a.closest('.image-set-div').parent(); //The imageset
    var index = getImageSetIndex($a);
    var otherElement = $b.closest('.image-set-div, .instruction-field, .instruction-field-history').parent(); //Other element
    var elementsBetween; //Elements between imageset and other element
    var direction; //True if up false if down
    var activeQueue = $('.ex-image-pair').eq(index); //Queue for active imageset
    //Checks which direction queue has to be moved and saves elements between dragged and dropped element
    if (activeImageSet.index() < otherElement.index()) { //Active imageset is above other element
        elementsBetween = activeImageSet.nextUntil(otherElement); //Elements between
        direction = false; //Queue needs to be moved down
    } else { //Active imageset is below other element
        elementsBetween = activeImageSet.prevUntil(otherElement); //Elements between
        direction = true; //Queue needs to be moved up
    }
    var imageSets = $('select > option:selected:not(:disabled)').closest('.image-set-div').parent();
    var notActiveImageSets = elementsBetween.not(imageSets); //Gets elements who aren't active imagesets
    var imageSetsBetween = elementsBetween.not(notActiveImageSets); //Removes other elements to get only active imagesets between
    //Moves the queue to the right position after imageset has been moved
    if (direction == false) { //Imagequeue has to be moved down
        $('.ex-image-pair').eq(index + imageSetsBetween.length).after(activeQueue);
    } else { //Imagequeue has to be moved up
        $('.ex-image-pair').eq(index - imageSetsBetween.length).before(activeQueue);
    }
}

/**
 * Adds experimentqueue to database based on step 4
 * @param {int} experimentId id of experiment
 */
function addExperimentQueue(experimentId) {
    var experimentQueueId = getExperimentQueueId();
    $('#ex-step4').find('div:eq(0)').children('div').each(function() {
        //If imageset
        if ($(this).find('.image-set').length == 1) {
            var imageSet = $(this).find('option:selected').attr('image-set');
            //If random queue
            if ($('#algorithm-type').val() != "Custom Queue") {
                var pictureQueueId; //Id of the picturequeue
                //Creates and adds pairing queue if pairing, else the entire pictureset
                if ($('#experiment-type').val() == "Paired comparison") {
                    pictureQueueId = generateRandomPictureQueue($('#experiment-type').val(), imageSet, +$('#same-pair').find('input').prop('checked'));
                }
                else if ($('#experiment-type').val() == "Triplet comparison") {
                    pictureQueueId = generateRandomPictureQueue($('#experiment-type').val(), imageSet, +$('#same-pair').find('input').prop('checked'));
                }
                else {
                    pictureQueueId = setPictureQueueRatingCategory(imageSet);
                }
                addPictureQueueToExperiment(pictureQueueId, experimentId);
            } else { //If custom queue

                var imageSets = $('select > option:selected:not(:disabled)').closest('.image-set-div').parent(); //Gets all active imagesets
                var index = imageSets.index($(this)); //Index of current imageset and imagequeue element
                var imageQueueElement = $('.image-set-queue').eq(index); //Gets the imagequeue element


                //If there is a new custom queue with pairs
                if (imageQueueElement.find('.ex-pair').length > 0) {
                    var pictureQueue = 0; //Function creates new when id = 0, then updates this to right id
                    var queueName = $(imageQueueElement).find('.queue-name').val();
                    imageQueueElement.find('.image-pairs').children('div.pair-div').each(function() {

                        pictureQueue = setPictureQueue([$(this).find('.first-image').children(':selected:not(:disabled)').attr('imageid'),
                            $(this).find('.second-image').children(':selected:not(:disabled)').attr('imageid')
                        ], 0, pictureQueue, queueName);
                    });
                    addPictureQueueToExperiment(pictureQueue, experimentId);
                } else { //If there is an existing queue
                    var queueId = imageQueueElement.find('option:selected:not(:disabled)').attr('imagequeue');
                    addPictureQueueToExperiment(queueId, experimentId);
                }
            }
            //If instruction
        } else if ($(this).children('.instruction-field').length == 1) {
            var instruction = $(this).find('input').val();
            addInstruction(instruction, experimentQueueId);
            //If instruction from history
        } else if ($(this).children('.instruction-field-history').length == 1) {
            var instruction = $(this).find('option:selected:not(:disabled)').attr('instruction');
            addInstructionHistory(instruction, experimentQueueId);
        }
    });
}

/**
 * Returns default inputfields
 * @return {array} array of intputfields
 */
function getDefaultInputFields() {
    var a;
    $.ajax({
        type: 'POST',
        async: false,
        dataType: 'json',
        url: 'ajax/scientist/getDefaultInputFields.php',
        success: function(data) {
            a = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return a;
}

/**
 * Adds default inputfields to step 3
 */
function setUpDefaultInputs() {
    var a = getDefaultInputFields();
    getDefaultInputFields().forEach(function(t) {
        var div = $('<div></div>');
        div.load('ajax/scientist/setupexperiment/defaultInputFields.html', function() {
            $(this).find('input:text').val(t['info']);
            $(this).find('input:text').attr('infoType', t['id']);
        });
        $('#ex-default-fieldset').append(div); //Adds new field at bottom
    });
}

/**
 * Adds a new instructions for current experiment
 * @param {string} text            text of instruction
 * @param {int} experimentQueue id of experimentqueue
 */
function addInstruction($text, $experimentQueue) {
    var a;
    $.ajax({
        type: 'POST',
        async: false,
        data: {
            'text': $text,
            'experimentQueue': $experimentQueue
        },
        url: 'ajax/scientist/addInstruction.php',
        success: function(data) {
            a = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return a;
}

/**
 * Adds a existing instruction to current experiment
 * @param {int} $id              id of instruction
 * @param {int} $experimentQueue id of experimentqueue
 */
function addInstructionHistory($id, $experimentQueue) {
    var a;
    $.ajax({
        type: 'POST',
        async: false,
        data: {
            'id': $id,
            'experimentQueue': $experimentQueue
        },
        url: 'ajax/scientist/addInstructionHistory.php',
        success: function(data) {
            a = data;
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return a;
}

/**
 * Returns queueid of current experiment
 * @return {int} id of experimentqueue
 */
function getExperimentQueueId() {
    var a;
    $.ajax({
        async: false,
        url: 'ajax/scientist/getExperimentQueueId.php',
        dataType: 'json',
        success: function(data) {
            a = data['id'];
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return a;
}

/**
 * Adds inputfields which are filled by user to current experiment in creation
 */
function addInputFields() {
    //Sets up the default inputfields and previous fields for experiment, as both use id's
    var defaultFields = [];
    $('.default-input-field').each(function() {
        if ($(this).parent().parent().find('input:checkbox').prop('checked') == true) {
            defaultFields.push($(this).attr('infotype'));
        }
    });
    //Sets up custom inputfields for experiment
    var fields = [];
    $('.custom-field').each(function() {
        if ($(this).val()) {
            fields.push($(this).val());
        }
    });
    //Sets up previously used inputfields for experiment
    $('.custom-field-history').each(function() {
        if ($(this).find('option:selected:not(:disabled)').length == 1) {
            defaultFields.push($(this).attr('id'));
        }
    });

    //Adds inputfields for experiment
    addObserverInputFields(defaultFields, fields);
}

/**
 * Adds categories to current experiment in creation
 * @param {string} $experimentId if of experiment (optional)
 */
function addCategories($experimentId) {
    //Sets up custom categories for experiment
    var fields = [];
    $('.ex-custom-category').each(function() {
        if ($(this).val()) {
            addCategory($(this).val(), $experimentId);
        }
    });
    //Sets up previously used categories for experiment
    $('.ex-category-history').each(function() {
        if ($(this).find('option:selected:not(:disabled)').length == 1) {
            addExistingCategory($(this).find('option:selected:not(:disabled)').attr('category'), $experimentId);
        }
    });
    //Adds inputfields for experiment
    //addCategoriesToExperiment(fields);
}
/**
 * Fixes queue when imageset and non imageset is switched
 * @param {type} $a active imageset
 * @param {type} $b non imageset, either instruction or inactive imageset
 * @returns {undefined}
 */
function setUpFinishButton(index) {
    var method = $('#experiment-type').val();
    var algorithm = $('#algorithm-type').val();
    if (index == 5) {
        $('.next-button').hide();
        $('.finish').show();
    } else if (index == 1) {
        $('.next-button').show();
        $('.previous-button').hide();
        $('.finish').hide();
    } else if (index == 4 &&
        (method == "Rank order" ||
            (method == "Paired comparison" && algorithm == "Random Queue") ||
            (method == "Artifact marking" && algorithm == "Random Queue")
        )
    ) {
        $('.next-button').hide();
        $('.finish').show();
    } else if (index == 4 && (method == "Rank order" || (method == "Triplet comparison" && algorithm == "Random Queue"))) {
        $('.next-button').show();
        $('.finish').hide();
    }
    else {
        $('.previous-button').show()
        $('.next-button').show();
        $('.finish').hide();
    }
}

/**
 * Finishes the experiment and uploads to database if all values are valid, else gives error message
 * @return {[type]} [description]
 */
function finishExperiment() {
    var method = $('#experiment-type').val();
    var check = 1;
    $('.notice').parent().remove();
    check = step4Check();
    if (method == "Category judgement" || method == "Triplet comparison") {
        if (!checkCategories()) {
            check = 0;
        }
    }
    if (method == "Paired comparison") {
        if ($('#algorithm-type').val() == "Custom Queue") {
            //Check if all queues got either a pair or selected queue
            if (checkPairs()) {
                check = 0;
            }
            if (imagePairCheck == 0) {
                if (checkMatchedPairs()) {
                    check = 0;
                    imagePairCheck = 1;
                }
            }
        }
    }
    if (check == 1) { //Ready to create experiment

        //Makes sure user doesn't doubleclick finish
        $('.finish').prop('disabled', true);
        delay(function() {
            $('.finish').prop('disabled', false);
        }, 2000);
        $.Notify({
            content: "Creating experiment...",
        });

        var expName = $("#ex-name").val();
        var expShortDesc = $("#ex-short-description").val();
        var expLongDesc = $("#ex-long-description").val();
        var expType = $('#experiment-type').children(':selected:not(:disabled)').index();
        var expScreenWhitePoint =  $('#ex-screen-whitepoint').val();
        var expLuminance = $('#ex-luminance-screen').val();
        var expRoomWhitepoint = $('#ex-room-whitepoint').val();
        var expAmbientIllumination = $('#ex-ambient-illumination').val();

        //Adding viewing distance as meta data
        var expViewingDistance = $('#ex-viewing-distance').val();

        var experimentId = startNewExperiment(expName,expShortDesc,expLongDesc,expType,expScreenWhitePoint,expLuminance,expRoomWhitepoint,expAmbientIllumination, expViewingDistance);

        //var experimentId = startNewExperiment($('#ex-name').val(), $('#ex-short-description').val(), $('#ex-long-description').val(), $('#experiment-type').children(':selected:not(:disabled)').index(), $('#ex-screen-whitepoint').val(), $('#ex-luminance-screen').val(), $('#ex-room-whitepoint').val(), $('#ex-ambient-illumination').val(),expViewingDistance);
        if (experimentId > 0) { //Got an id, successfully created experiment
            var hidden = +$('#hidden-experiment').find('input').prop('checked');
            hidden = (hidden == 1) ? '0 = Hidden' : '1 = Public';

            //Sets up parameters for experiment
            addObserverParametersPair($("#background-color").val(), +!$('#forced-pick').find('input').prop('checked'), +$('#same-pair').find('input').prop('checked'), +$('#display-original').find('input').prop('checked'), +$('#allow-colourblind').find('input').prop('checked'), hidden, +$('#display-clock').find('input').prop('checked'));

            if (method == "Category judgement" || method == "Triplet comparison") {
                addCategories(experimentId);
            }
            addInputFields();
            addExperimentQueue(experimentId);
            delay(function() {
                $.Notify({
                    content: "Experiment successfully created",
                    style: {
                        background: 'lime'
                    },
                });
                delay(function() {
                    $("#dashboard").trigger('click'); //Navigates to dashboard
                }, 1500);
            }, 300);
        } else { //If error
            delay(function() {
                $.Notify({
                    content: "Error creating experiment, please refresh and try again",
                    style: {
                        background: 'red',
                        color: 'white'
                    },
                });
                $('.finish').prop('disabled', false);
            }, 300);
        }
    }
}

/**
 * Makes sure all queues got pairs
 * @return {int} returns false if all pairs are ok, else true and displys error message
 */
function checkPairs() {
    //Check if all pairs are filled
    if ($('.first-image > option:selected:disabled').length > 0 || $('.second-image > option:selected:disabled').length > 0) {
        $('#ex-image-sets').after('<div id="notify"><div class="span3" style="margin: 20px 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'A new custom queue requires a name' + '</div></div>');
        return true
    }
    //Get all queues with selected queue
    var queuesWithQueue = $('.existing-queue').find('option:selected:not(:disabled)').closest('.ex-image-pair');
    //Get all queues with added pairs
    var queuesWithPairs = $('.ex-pair').closest('.image-set-queue');
    //Check if all queues got pairs or selected queue
    if (($('.image-set-queue').not(queuesWithPairs).find('.existing-queue').find('option:selected:disabled').length > 0) || (queuesWithPairs.length < $('.image-set-queue').not(queuesWithQueue.children('.image-set-queue')).length)) {
        $('#ex-image-sets').after('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'All queues require either pairs or a selected queue' + '</div></div>');
        return true
    }
    //Check if there are queues without names, whom doesn't have a queue selected
    if (($('.queue-name:text').filter(function() {
        return $(this).val() == ""
    }).closest('.ex-image-pair').not(queuesWithQueue).length > 0)) { //If there are queues without names
        $('#ex-image-sets').after('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'All custom picturequeues needs a name' + '</div></div>');
        return true;
    }
    return false;
}

/**
 * Makes sure all pairs are matched with each other
 * @return {int} 0 if no pairs missing, else a positive value
 */
function checkMatchedPairs() {
    var missingPairs = 0;
    $('.image-pairs').each(function() {
        var images = new Array();
        var firstSet = new Array(); //images left in pair
        var secondSet = new Array(); //images right in pair
        var check = 0; //Local check if images are missing in current imageset
        var instances;
        //Get all images
        $(this).find('.first-image').eq(0).children(':not(:disabled)').each(function() {
            images.push($(this).attr('imageid'));
        });
        //Gets all first images
        $(this).find('.first-image').each(function() {
            firstSet.push($(this).children('option:selected:not(:disabled)').attr('imageid'));
        });
        //Gets all second images
        $(this).find('.second-image').each(function() {
            secondSet.push($(this).children('option:selected:not(:disabled)').attr('imageid'));
        });
        //Removes duplicates/the same images matched with eachother
        for (var i = 0; i < firstSet.length; i++) {
            if (firstSet[i] == secondSet[i]) {
                firstSet.splice(i, 1);
                secondSet.splice(i, 1);
                i--;
            }
        }
        //Get all instances
        instances = countImages(firstSet.concat(secondSet));
        //Checks if there are missing instances
        for (var i = 0; i < images.length; i++) {
            if ($.inArray(images[i], instances[0]) == -1) {
                check++;
                missingPairs++;
            }
        }
        //Checks if the count for each instanced image is amount-1
        for (var i = 0; i < instances[1].length; i++) {
            if (instances[1][i] < images.length - 1) {
                check++;
                missingPairs++;
            }
        }
        if (check != 0) {
            $(this).append('<div><div class="span3" style="margin: 20px 20px"></div>' + '<div class="bg-red notice image-queue-check marker-on-top span1">' + 'Not all images are matched! Press finish again to continue, or this box to check again' + '</div></div>');
        }
    });
    return missingPairs;
}
/**
 *  Counts and returns amount of images in array with objects
 *  @author Sime Vidas http://stackoverflow.com/questions/5667888/counting-occurences-of-javascript-array-elements
 *
 **/
function countImages($arr) {
    var a = [],
        b = [],
        prev;
    $arr.sort();
    for (var i = 0; i < $arr.length; i++) {
        if ($arr[i] !== prev) {
            a.push($arr[i]);
            b.push(1);
        } else {
            b[b.length - 1]++;
        }
        prev = $arr[i];
    }
    return [a, b];
}
/**
 * Makes sure user has at least two categories, and no categories has the same name
 * @return {int} 1 if pass, 0 if error
 */
function checkCategories() {
    var AMOUNT_REQUIRED = 2;
    var categories;
    var categoryValues = new Array();
    //Gets all custom categories without empty input
    var custom = $('.ex-custom-category[type=text]').filter(function() {
        return this.value;
    });
    //Gets all previous categories selected
    var history = $(".ex-category-history > :selected:not(:disabled)");
    categories = $(custom).add($(history)); //Saves all categories in a single variable
    //Checks if there is enough categories
    if (categories.length < AMOUNT_REQUIRED) {
        $('#ex-add-category').before('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + 'Minimum 2 categories required' + '</div></div>');
        return 0;
    }
    //Adds each category to a array, or returns if the categoryname already exists
    categories.each(function() {
        if (categoryValues.indexOf($(this).val()) == -1) {
            categoryValues.push($(this).val());
        } else {
            return false;
        }
    });
    //If values and elements matches, there was no duplicates
    if (categoryValues.length == categories.length) {
        return 1;
    } else {
        $('#ex-add-category').before('<div id="notify"><div class="span3" style="margin: 0 20px"></div>' + '<div class="bg-red notice marker-on-top span1">' + "Categories can't have the same name" + '</div></div>');
        return 0;
    }
}
