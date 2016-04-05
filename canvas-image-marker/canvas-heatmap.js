(function($) {
    'use strict';

    $.fn.canvasHeatmap = function(options) {
        this.each(function(canvasIndex, element) {
            // first argument sets the context of "this" in the initCanvasMarkingTool function
            initCanvasHeatmap.apply(element, [canvasIndex, element, options]);
        });
    };

    var initCanvasHeatmap = function(canvasIndex, element, options) {
        // establish our default settings, override if any provided
        var settings = $.extend({
            imageUrl: $(this).attr('data-image-url'),
        }, options);

        // get the current database id of the experiment
        var experimentID = $(this).attr('data-experiment-id');
        // get the current database id of the image
        // var pictureID = $(this).attr('data-picture-id');

        // canvas where the drawing will take place
        var canvas = $('<canvas>');
        var ctx = canvas[0].getContext('2d');

        // put our savedShapes here, so we don't have to redraw them every time something changes
        var savedCanvas = $('<canvas>');
		var savedCtx = savedCanvas[0].getContext('2d');

        var canvasContainer = element;
        var image;

        $(document).ready(function() {
            setCanvasImage();
            $(canvasContainer).append(savedCanvas); // append the resized canvas to the DOM
            $(canvasContainer).append(canvas); // append the resized canvas to the DOM
            // $(canvasContainer).after(matrixCanvas); // append the resized canvas to the DOM
        });

        /**
         * Figure out the size of the image, so we can set the canvas to the same size.
         */
        function setCanvasImage() {
            image = new Image();

            var resize = function() {
                // make all elements the same size as the image
                $('.canvas-container').css({
                    height: image.height,
                    width: image.width
                });
                canvas.attr('height', image.height).attr('width', image.width);
                savedCanvas.attr('height', image.height).attr('width', image.width);
            };

            $(image).load(resize);
            image.src = settings.imageUrl;

            if (image.loaded)
                resize();

            savedCanvas.css({
                background: 'url(' + image.src + ')',
                position: "absolute", top: 0, right: 0
            });

            canvas.css({
                position: "absolute", top: 0, right: 0
            });
        }


        /*--------------------------------------------------------*
         *				Fill Algorithm for Polygon                *
         *--------------------------------------------------------*/


    }; // end of initCanvasMarkingTool()

})(jQuery);
