var Annotation = function() {
    this.annotationBox;
    this.annotationTextarea;
    this.annotationSaveButton;
    this.annotationCloseButton;
};

Annotation.prototype.createAnnotation = function(appendContainer, canvasIndex) {
    // create and append a annotation box to the canvas
    var annotation = [
        '<div class="annotation" data-id="" canvas-id="' + canvasIndex + '">',
            '<div class="annotationButtons">',
                '<button class="saveAnnotation"><i class="fa fa-check"></i></button>',
                '<button class="closeAnnotation"><i class="fa fa-times"></i></button>',
            '</div>',
            '<textarea class="annotationText" placeholder="What do you see?" value=""></textarea>',
        '</div>'
    ].join('');
    $(appendContainer).append(annotation);

    // get all the annotation elements
    this.annotationBox = $('.annotation[canvas-id=' + canvasIndex + ']');
    this.annotationTextarea = this.annotationBox.find('.annotationText');
    this.annotationSaveButton = this.annotationBox.find('.saveAnnotation');
    this.annotationCloseButton = this.annotationBox.find('.closeAnnotation');

    // register events
    var self = this;
    this.annotationCloseButton.on('click', function(e) {
        self.closeAnnotation(e);
    });
};

/**
 * Open and display the annotation box at the user's mouse position.
 * As well as displaying any previous set annotation text.
 */
Annotation.prototype.openAnnotation = function(mouseY, mouseX, shapeIndex, canvasIndex, annotationText) {
    this.annotationBox.css({
        'display': 'block',
        'top': ( mouseY - (this.annotationBox.outerHeight() + 9) ) + "px",
        'left': ( mouseX - (this.annotationBox.outerWidth() / 2) ) + "px"
    });

    this.annotationBox.attr('data-id', shapeIndex);
    this.annotationTextarea.val(annotationText);
};

/**
 * Close the annotation box.
 */
Annotation.prototype.closeAnnotation = function(e) {
    e.preventDefault();
    this.annotationBox.css('display', 'none');
};
