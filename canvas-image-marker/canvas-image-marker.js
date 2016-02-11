(function($) {
    $.fn.canvasAreaDraw = function(options) {
        this.each(function(index, element) {
            init.apply(element, [index, element, options]);
        });
    }

    var init = function(index, canvasContainer, options) {
        var settings;
        var canvas, ctx, image;

        var draw, mousedrag, stopdrag, move, resize, reset, undo;

        var activePoint;
        var points = [];
        var savedShapes = [];

        canvas = $('<canvas>');
        ctx = canvas[0].getContext('2d');

        image = new Image();
        resize = function() {
            canvas.attr('height', image.height).attr('width', image.width);
        };
        $(image).load(resize);
        image.src = $('.canvas-container').attr('data-image-url');
        if (image.loaded) resize();
        canvas.css({ background: 'url(' + image.src + ')' });

        $(document).ready( function() {
            $(canvasContainer).append(canvas); /* append the resized canvas to the DOM */

            $('#undo').on('click', undo);
            $('#reset').on('click', reset);

            canvas.on('mousedown', startdrag);
            canvas.on('mouseup', stopdrag);

            $(document).keydown(function(e) {
                if (e.keyCode == 27) undo();
            });
        });

        reset = function() {
            points = [];
            savedShapes = [];
            draw();
        };

        stopdrag = function() {
            $(this).off('mousemove');

            saveShape();
            console.log(savedShapes[0].points.toString());
        };

        var startdrag = function() {
            $(this).on('mousemove', mousedrag);
        };


        /**
         * Set distance of each point.
         * Effectively decreasing or increasing precision of the marking.
         *
         * @return {boolean} false
         */
        mousedrag = function(e) {
            e.preventDefault();

            var x;
            var y;
            var dis;
            var insertAt = points.length;

            x = e.offsetX;
            y = e.offsetY;

            for (var i = 0; i < points.length; i+=2) {
                dis = Math.sqrt(Math.pow(x - points[i], 2) + Math.pow(y - points[i+1], 2));
                if ( dis < 8 ) {
                    activePoint = i;
                    return false;
                }
            }

            points.splice(insertAt, 0, Math.round(x), Math.round(y));
            activePoint = insertAt;

            draw();

            return false;
        };

        /**
         * Takes the values from the points and savedShapes array,
         * and draws it on the canvas.
         *
         * @return void
         */
        draw = function() {
            /* clear the canvas each time */
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

            /* if we have any saved shapes, draw them first */
            if (savedShapes.length > 0) {
                ctx.fillStyle = 'rgb(255, 255, 255)';
                ctx.strokeStyle = 'rgb(116, 175, 91)';
                ctx.lineWidth = 2;

                for (var k = 0; k < savedShapes.length; k++) {
                    ctx.beginPath();
                    ctx.moveTo(savedShapes[k].points[0], savedShapes[k].points[1]);
                    for (var d = 0; d < savedShapes[k].points.length; d += 2) {
                        if (savedShapes[k].points.length > 2 && d > 1) {
                            ctx.lineTo(savedShapes[k].points[d], savedShapes[k].points[d + 1]);
                        }
                    }
                    ctx.closePath();
                    ctx.fillStyle = 'rgba(0, 100, 0, 0.3)';
                    ctx.fill();
                    ctx.stroke();
                }
            }

            /* draw current path (shape) */
            ctx.fillStyle = 'rgb(255, 255, 255)';
            ctx.strokeStyle = 'rgb(249, 49, 49)';
            ctx.lineWidth = 2;

            ctx.beginPath();
            ctx.moveTo(points[0], points[1]);
            for (var i = 0; i < points.length; i+=2) {
                if (points.length > 2 && i > 1) {
                    ctx.lineTo(points[i], points[i+1]);
                }
            }
            ctx.fillStyle = 'rgba(250, 250, 250, 0.3)';
            ctx.fill();
            ctx.stroke();
        };

        /**
         * Save the shape by putting all the points from the points array into the savedShape array,
         * and then emptying the points array.
         *
         * @return void
         */
        saveShape = function() {
            /* save all the x and y coordinates as well as any comment */
            savedShapes.push({
                points: points,
                comment: "Hello from the other side"
            });
            points = [];
            draw();
        };

        /**
         * Delete one shape, by removing the last array element.
         *
         * @return void
         */
        undo = function() {
            if (points.length > 0)
                points = [];
            else
                savedShapes.pop();

            draw();
        };

    }; // end of init()


    /* Bootstrap the canvas. Let's go! */
    $(document).ready(function() {
        $('.canvas-container[data-image-url]').canvasAreaDraw();
    });

})(jQuery);
