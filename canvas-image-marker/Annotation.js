var Annotation = function(annotationElement, annotationText) {
    this.annotation = annotationElement;
    this.annotationText = annotationText;
};

/**
 * Get the value from the data-id attribute of the annotation container,
 * and update the savedShape at the corresponding array index.
 */
Annotation.prototype.saveAnnotation = function(e) {
    e.preventDefault();

    var shapeID = this.annotation.attr('data-id');
    var annoText = $.trim(this.annotationText.val());

    /* get the annotation text and update the saved shape with that id */
    savedShapes[shapeID].annotation = annoText;

    closeAnnotation(e);
};

/**
 * Open and display the annotation box at the user's mouse position.
 * As well as displaying any previous set annotation text.
 */
Annotation.prototype.openAnnotation = function(mouseY, mouseX, shapeIndex) {
    var self = this;
    this.annotation.css({
        'display': 'block',
        'top': ( mouseY - (this.annotation.outerHeight() + 9) ) + "px",
        'left': ( mouseX - (this.annotation.outerWidth() / 2) ) + "px"
    });
    // this.annotation.attr('data-id', shapeIndex);
    // this.annotationText.val(savedShapes[shapeIndex].annotation);

    // var annotationBox = [
    //     '<div class="annotation" data-id="' + shapeIndex + '">',
    //         '<div id="annotationButtons">',
    //             '<button id="saveAnnotation"><i class="fa fa-check"></i></button>',
    //             '<button id="closeAnnotation"><i class="fa fa-times"></i></button>',
    //         '</div>',
    //         '<textarea id="annotationText" placeholder="What do you see?" value="' + savedShapes[shapeIndex].annotation + '"></textarea>',
    //     '</div>'
    // ].join('');
    //
    // $(".canvas-container").prepend(annotationBox);
};

/**
 * Close the annotation box.
 */
Annotation.prototype.closeAnnotation = function(e) {
    e.preventDefault();
    this.annotation.css('display', 'none');
};

// var Annotation = new Annotation($('.annotation'), $('.annotationText'));
// Annotation.openAnnotation(500, 500, 1);
