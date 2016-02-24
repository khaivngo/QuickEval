(function($) {
    var initCanvasMarkerTool = function() {
        var canvas = $('<canvas>');
        var ctx = canvas[0].getContext('2d');
        var annotation = $('.annotation');
        var annotationText = $('#annotationText');
        var canvasContainer = $('.canvas-container');
        var image;
        var points = [];
        var savedShapes = [];

        $(document).ready( function() {
            setCanvasImage();

            $(canvasContainer).append(canvas); /* append the resized canvas to the DOM */
            canvas.on('mousedown', startdrag);
            canvas.on('mouseup', stopdrag);
            canvas.on('mousedown', click);

            $('#undo').on('click', undo);
            $('#reset').on('click', reset);
            $('#saveAnnotation').on('click', saveAnnotation);
            $('#closeAnnotation').on('click', closeAnnotation);

            // params: element which will be moved, element which is dragable
            makeDraggable( ".annotation", "#annotationButtons" );
        });

        var setCanvasImage = function() {
            image = new Image();

            resize = function() {
                canvas.attr('height', image.height).attr('width', image.width);
            };

            $(image).load(resize);
            image.src = canvasContainer.attr('data-image-url');

            if (image.loaded)
                resize();

            canvas.css({ background: 'url(' + image.src + ')' });
        };

        /**
         * Get the value from the data-id attribute of the annotation container,
         * and update the savedShape at the corresponding array index.
         */
        var saveAnnotation = function(e) {
            e.preventDefault();

            var shapeID = annotation.attr('data-id');
            var annoText = $.trim(annotationText.val());

            /* get the annotation text and update the saved shape with that id */
            savedShapes[shapeID].annotation = annoText;

            closeAnnotation(e);
        };

        var closeAnnotation = function(e) {
            e.preventDefault();
            annotation.css('display', 'none');
        };

        var click = function(e) {
            var mouseX  = e.offsetX;
            var mouseY  = e.offsetY;

            ctx.lineWidth = 2;

            for (var k = 0; k < savedShapes.length; k++) {
                ctx.beginPath();
                ctx.moveTo(savedShapes[k].points.x, savedShapes[k].points.y);

                for (var d = 0; d < savedShapes[k].points.length; d++) {
                    ctx.lineTo(savedShapes[k].points[d].x, savedShapes[k].points[d].y);
                }

                if (ctx.isPointInPath(mouseX, mouseY)) {
                    annotation.css({
                        'display': 'block',
                        'top': (mouseY - 140) + "px",
                        'left': (mouseX - 125) + "px"
                    });
                    annotation.attr('data-id', k);
                    annotationText.val(savedShapes[k].annotation);

                    return; /* we found the clicked polygon, no need to loop through the rest */
                }
                ctx.closePath();
            }
        };

        var stopdrag = function(e) {
            $(this).off('mousemove');
            /* we're done drawing, save the shape */
            saveShape(e);
        };

        var startdrag = function() {
            $(this).on('mousemove', mousedrag);
        };

        /**
         * Calls the drawing function each time the mouse
         * has been dragged a certain distance.
         *
         * @return {boolean} false
         */
        var mousedrag = function(e) {
            e.preventDefault();

            var dis;
            var x = e.offsetX;
            var y = e.offsetY;

            for (var i = 0; i < points.length; i++) {
                dis = Math.sqrt(Math.pow(x - points[i].x, 2) + Math.pow(y - points[i].y, 2));
                if ( dis < 6 ) {
                    // return if we do not have enough distance
                    return false;
                }
            }

            points.push({ x: Math.round(x), y: Math.round(y) });

            draw();

            return false;
        };

        /**
         * Takes the values from the points and savedShapes array,
         * and draws it on the canvas.
         *
         * @return void
         */
        var draw = function() {
            /* clear the canvas each time */
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

            drawSavedShapes();

            if (points.length > 0) {
                ctx.fillStyle = 'rgba(0, 0, 100, 0.3)';
                ctx.strokeStyle = 'rgb(0, 0, 100)';
                ctx.lineWidth = 2;

                ctx.beginPath();
                ctx.moveTo(points[0].x, points[0].y);

                for (var i = 0; i < points.length; i++) {
                    if (points.length > 1) {
                        ctx.lineTo(points[i].x, points[i].y);
                    }
                }

                ctx.fill();
                ctx.stroke();
            }
        };

        var drawSavedShapes = function() {
            if (savedShapes.length > 0) {
                ctx.fillStyle = 'rgba(0, 100, 0, 0.3)';
                ctx.strokeStyle = 'rgb(116, 175, 91)';
                ctx.lineWidth = 2;

                for (var k = 0; k < savedShapes.length; k++) {
                    ctx.beginPath();
                    ctx.moveTo(savedShapes[k].points.x, savedShapes[k].points.y);

                    for (var d = 0; d < savedShapes[k].points.length; d++) {
                        ctx.lineTo(savedShapes[k].points[d].x, savedShapes[k].points[d].y);
                    }
                    ctx.closePath();
                    ctx.fill();
                    ctx.stroke();
                }
            }
        };

        /**
         * Save the shape by putting all the points from the points array into the savedShape array,
         * and then emptying the points array.
         *
         * @return void
         */
        var saveShape = function(e) {
            // only save the shape if we have atleast 3 points
            if (points.length > 2) {
                // save all the x and y coordinates as well as any comment
                savedShapes.push({ points: points, annotation: "" });
            }

            points = []; /* remove the current shape now that it's saved */

            draw();
        };

        /**
         * Delete one shape, by removing the last array element.
         *
         * @return void
         */
        var undo = function() {
            if (points.length > 0)
                points = [];
            else
                savedShapes.pop();

            draw();
        };

        var reset = function() {
            points = [];
            savedShapes = [];

            draw();
        };

        /**
         * Make any element draggable.
         *
         * @param {string} id or class name of html element which is grabable
         * @param {string} id or class name of html element to be moved
         */
        var makeDraggable = function(grabable, moveable) {
            $(grabable).on('mousedown', moveable, function() {
                $(grabable).addClass('draggable').parents().on('mousemove', function(e) {
                    e.preventDefault();
                    $('.draggable').offset({
                        top: e.pageY - $('.draggable').outerHeight() / 2,
                        left: e.pageX - $('.draggable').outerWidth() / 2
                    }).on('mouseup', function() {
                        $(grabable).removeClass('draggable');
                    });
                });
            }).on('mouseup', function() {
                $('.draggable').removeClass('draggable');
            });
        };

    }; // end of init()


    /* Let's go. */
    $(document).ready(function() {
        initCanvasMarkerTool();
    });

})(jQuery);
