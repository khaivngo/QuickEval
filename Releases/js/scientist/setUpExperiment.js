$(document).ready(function() {
    $("#set-up-experiment").click(function() {
        inject("ajax/scientist/setupExperiment.html")
        setActive($(this));
        $('#stepper').stepper({
            onStep: function(index, step) {
                $('[id^=ex-step]').hide();
                $('#ex-step' + index).fadeIn();
            }
        });
        //---------------- STEP 1 --------------------//

        $('#ex-optional-fields').hide(); //Hides step 1 optional fields
        $('#ex-description-up').hide(); //Hides the visual "down"-icon
        $('#ex-open-optional-fields').click(function() {
            $('#ex-optional-fields').slideToggle(); //Toggles the optional fields
            $('#ex-description-up').toggle(); //Changes direction on the up/down-icons
            $('#ex-description-down').toggle();
        });
        //---------------- STEP 2 --------------------//

        $('#ex-add-field').click(function() {    //Displays an additional field for the scientist to use
            customFieldAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addCustomField.html');
            $(this).before(div);
        });
        $('#ex-add-field-history').click(function() {    //Displays dropdown menu from previously used fields for
            customFieldAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addCustomFieldFromHistory.html');
            $('#ex-add-field').before(div); // scientist to use
        });
        //---------------- STEP 3 --------------------//

        $('#ex-add-image-set').click(function() {    //Adds a dropdown menu with image sets
            experimentQueueAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/imageSetDropDown.html', function() {
                $('#ex-add-image-set').before(div);
                $('.dropdown-menu').dropdown();
                //$('#ex-step3').page();
                var currentSet = $('.ex-image-set-inner').last();
                var width = $('.ex-img').outerWidth(true) * (currentSet.children().length + 1);
                $('.ex-image-set-inner').css("width", width);
                $('.ex-image-order').hide();
            });
        });
        $('#ex-add-instruction').click(function() {
            customFieldAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addInstructionField.html');
            $('#ex-add-image-set').before(div);
        });
        $('#ex-add-instruction-history').click(function() {
            experimentQueueAmount++;
            var div = $('<div></div>');
            div.load('ajax/scientist/setupexperiment/addInstructionFromHistory.html');
            $('#ex-add-image-set').before(div);
        });
        //------------------------------------------------------------------Parameters--------------------------------------------------------------------


    });
})

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