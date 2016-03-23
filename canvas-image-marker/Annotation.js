var Annotation = function() {
    this.annotationBox;
    this.annotationTextarea;
    this.annotationButtonBar;
    this.annotationDragHandle;
    this.annotationSaveButton;
    this.annotationCloseButton;
};

/**
 * Create and append the HTML annotation element. Set all class properties
 * when the element is created.
 */
Annotation.prototype.createAnnotation = function(appendContainer, canvasIndex) {
    // create and append a annotation box to the canvas
    $(appendContainer).append(
        '<div class="annotation" data-id="" canvas-id="' + canvasIndex + '">' +
            '<div class="annotationButtons">' +
                '<div class="dragHandle"></div>' +
                '<button class="saveAnnotation"><i class="fa fa-check"></i></button>' +
                '<button class="closeAnnotation"><i class="fa fa-minus-square-o"></i></button>' +
            '</div>' +
            '<textarea class="annotationText" placeholder="What do you see?" value=""></textarea>' +
        '</div>'
    );

    // get all the annotation elements
    this.annotationBox = $('.annotation[canvas-id=' + canvasIndex + ']');
    this.annotationTextarea = this.annotationBox.find('.annotationText');
    this.annotationButtonBar = this.annotationBox.find('.annotationButtons');
    this.annotationDragHandle = this.annotationBox.find('.dragHandle');
    this.annotationSaveButton = this.annotationBox.find('.saveAnnotation');
    this.annotationCloseButton = this.annotationBox.find('.closeAnnotation');

    // enable the annotation box to be draggable
    var helper = new Helper;
    helper.makeDraggable(this.annotationDragHandle, this.annotationBox);

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
        'position': 'absolute',
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
