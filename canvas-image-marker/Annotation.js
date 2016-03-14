var Annotation = function(canvasID) {
    this.annotationBox = [
        '<div class="annotation" data-id="" canvas-id="' + canvasID + '">',
            '<div id="annotationButtons">',
                '<button id="saveAnnotation"><i class="fa fa-check"></i></button>',
                '<button id="closeAnnotation"><i class="fa fa-times"></i></button>',
            '</div>',
            '<textarea id="annotationText" placeholder="What do you see?" value=""></textarea>',
        '</div>'
    ].join('');
};

Annotation.prototype.createAnnotation = function(appendContainer) {
    $(appendContainer).append(this.annotationBox);
};

/**
 * Open and display the annotation box at the user's mouse position.
 * As well as displaying any previous set annotation text.
 */
Annotation.prototype.openAnnotation = function(mouseY, mouseX, shapeIndex, canvasIndex, annotationText) {
    var self = this;
    // $(canvasContainer).append($(self.annotationBox));

    console.log($('.annotation').attr('canvas-id', canvasIndex));

    // $(self.annotationBox).attr('data-id', shapeIndex);
    // $(self.annotationBox).val(annotationText);

    // $(canvasContainer).append($(self.annotationBox));

};


/**
 * Get the value from the data-id attribute of the annotation container,
 * and update the savedShape at the corresponding array index.
 */
Annotation.prototype.saveAnnotation = function() {
    // var shapeID = this.annotation.attr('data-id');
    // var annoText = $.trim(this.annotationText.val());
    //
    // /* get the annotation text and update the saved shape with that id */
    // savedShapes[shapeID].annotation = annoText;
    //
    // closeAnnotation(e);
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
