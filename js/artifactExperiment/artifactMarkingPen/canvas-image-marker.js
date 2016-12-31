(function($) {
    'use strict';

    $.fn.canvasMarkingTool = function(options) {
        this.each(function(canvasIndex, element) {
            // first argument sets the context of "this" in the initCanvasMarkingTool function
            initCanvasMarkingTool.apply(element, [canvasIndex, element, options]);
        });
    };

    var initCanvasMarkingTool = function(canvasIndex, element, options) {
        // establish our default settings, override if any provided
        var settings = $.extend({
            imageUrl: $(this).attr('data-image-url'),
            annotation: false,
            placeholderText: "What do you see?"
        }, options);

        /* get the current database id of the experiment */
        var experimentID = $(this).attr('data-experiment-id');
        /* get the current database id of the image */
        var pictureID = $(this).attr('data-picture-id');
        /* get the picture queue */
        var pictureQueue = $(this).attr('id');

        /* save a reference to this canvas container element, so we can still manipulate it later */
        var self = $(this);

        // listen for updates on the data-picture-queue attribute on the .canvas-container element
        // whenever data-picture-queue has a new value, the user has gone to the next step in
        // the experiment. So update all the values in this plugin to reflect the changes.
        $(document).on('data-attribute-changed', function() {
            pictureQueue = self.attr('id');
            pictureID = self.attr('data-picture-id');

            settings.imageUrl = self.attr('data-image-url');
            setCanvasImage();
        });

        // canvas where the drawing will take place
        var canvas = $('<canvas>');
        var ctx = canvas[0].getContext('2d');

        // put our savedShapes on another canvas,
        // so we don't have to redraw them every time something changes
        var savedCanvas = $('<canvas>');
		var savedCtx = savedCanvas[0].getContext('2d');

        var toolPanel = $(
            '<div class="marking-tool-panel">' +
                '<div class="mode">' +
                    '<button title="Make image movable" style="font-size: 25px; background: #eee;" class="tool-button enable-panzoom down-arrow"><i class="fa fa-arrows"></i></button>' +
                    '<button title="Enable drawing tool" style="font-size: 25px;" class="tool-button enable-marking"><i class="fa fa-pencil-square-o"></i></button>' +
                    '<div class="mode marking-tools">' +
                        '<button title="Pen tool" class="tool-button draw-tool down-arrow" style="background: #eee;"><i class="fa fa-pencil"></i></button>' +
                        '<button title="Erase tool" class="tool-button erase-tool"><i class="fa fa-scissors"></i></button>' +
                        '<button title="Undo last drawing" class="undo"><i class="fa fa-undo"></i></button>' +
                    '</div>' +
                '</div>' +
            '</div>');

        var canvasContainer = element;
        var image;

        var points = [];
        var savedShapes = [];
        var deleteArea = [];

        var TOOL = "MARKER"; /* keeps track of whether the users is drawing or erasing */

        $(document).ready(function() {
            setCanvasImage();
            $(canvasContainer).append(savedCanvas); // append the resized canvas to the DOM
            $(canvasContainer).append(canvas); // append the resized canvas to the DOM

            if (settings.showToolbar == true) {
                $('body').find('.marking-tool-panel').remove();
                // $(canvasContainer).parent().parent().parent().prepend(toolPanel);
                $('.pen-menu').append(toolPanel);
            }

            toolPanel.find('.undo').on('click', undo);
            toolPanel.find('.marking-tool').on('click', setMarkingActiveTool);
            toolPanel.find('.draw-tool').on('click', setMarkingActiveTool);
            toolPanel.find('.erase-tool').on('click', setEraseActiveTool);
            toolPanel.find('.enable-marking').on('click', setModeToPanning);
            toolPanel.find('.enable-panzoom').on('click', setModeToDrawing);

            toolPanel.find('.marking-tools .tool-button').on('click', function() {
                toolPanel.find('.marking-tools .tool-button').removeClass('down-arrow');
                toolPanel.find('.marking-tools .tool-button').css('background', '#ccc');

                $(this).css('background', '#eee');
                $(this).addClass('down-arrow');
            });

            toolPanel.find('.marking-tools .tool-button').on('click', function() {
                toolPanel.find('.marking-tools .tool-button').removeClass('down-arrow');
                toolPanel.find('.marking-tools .tool-button').css('background', '#ccc');

                $(this).css('background', '#eee');
                $(this).addClass('down-arrow');
            });


            $('#button-next-category').on('click', sendMarkToDB);
        });

        var sendMarkToDB = function() {
            savedShapes.forEach(function(shape) {
                var fillAsJSONstring = JSON.stringify(shape.fill);
                $.ajax({
                    url: 'ajax/observer/insertArtifactMark.php',
                    type: 'POST',
                    data: {
                        mark: fillAsJSONstring,
                        remark: shape.annotation,
                        experiment_id: experimentID,
                        picture_id: pictureID,
                        picture_queue: pictureQueue
                    },
                    dataType: 'json',
                    encode: true,
                    cache: false,
                    processData: true
                })
                .done(function(response) {})
                .fail(function(response) {});
            });

            // delete all savedShapes in order to make it ready for the next image set
            reset();
        };

        /**
         * Figure out the size of the image, so we can set the canvas to the same size.
         */
        var setCanvasImage = function() {
            image = new Image();

            var resize = function() {
                // make all elements the same size as the image
                $('.canvas-container').css({
                    height: image.height + 'px',
                    width: image.width + 'px'
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
                position: "absolute",
                top: 0,
                right: 0
            });

            canvas.css({
                position: "absolute",
                top: 0,
                right: 0
            });
        };

        /**
         * Enable canvas drawing, turn off panning.
         */
        var setModeToPanning = function() {
            enableMarking();

            toolPanel.find('.enable-marking').css('background', '#eee');
            toolPanel.find('.enable-panzoom').removeClass('down-arrow');

            toolPanel.find('.marking-tools').css("display", "flex");
            canvas.css({ cursor: "default" });

            // disable panning of the images so we can use the marking tool on the image
            $(".panzoom").panzoom("option", "disablePan", true);
        };

        /**
         * Enable panning, turn off canvas drawing.
         */
        var setModeToDrawing = function() {
            disableMarking();

            canvas.css({ cursor: "move" });

            toolPanel.find('.enable-panzoom').addClass('down-arrow');
            toolPanel.find('.enable-marking').removeClass('right-arrow');
            toolPanel.find('.marking-tools').css("display", "none");

            // disable panning of the images so we can use the marking tool on the image
            $(".panzoom").panzoom("option", "disablePan", false);
        };

        var setEraseActiveTool = function() {
            TOOL = "DELETE";
        };

        var setMarkingActiveTool = function() {
            TOOL = "MARKER";
        };

        /**
         * Enable the drawing events.
         */
        var enableMarking = function() {
            canvas.on('mousedown', startdrag);
            canvas.on('mouseup', stopdrag);
            canvas.on('dblclick', findClickedShape);
        };

        /**
         * Disable the drawing events.
         */
        var disableMarking = function() {
            canvas.off('mousedown');
            canvas.off('mouseup');
            canvas.off('dblclick');
        };

        /**
         * Assemble a Shape object.
         */
		var Shape = function(points, annotation) {
            this.points = points;
            this.annotation = annotation;
            this.fill;

            this.setFill = function() {
                this.fill = calcFill(this);
            }
        };

        /**
         * Whenever a double click event takes place on the canvas this function
         * checks whether the click is inside a shape.
         *
         * Note: We have to redraw before we can call isPointInPath()
         */
        var findClickedShape = function(e) {
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
                    openAnnotationModal(k, savedShapes[k].annotation);
                    break; /* we found the clicked polygon, no need to loop through the rest */
                }
                ctx.closePath();
            }
        };

        /**
         * Create and open the annotation modal.
         *
         * @return void
         */
        function openAnnotationModal(id, text) {
            var inputModal = new AnnotationModal({
                /* display the text in the input field, if the selected shape already has a annotation saved */
                inputValue: ( (text != '') ? text : '' ),
                /* placholder="" attribute text of the input field */
                placeholderText: settings.placeholderText,
                /* save the annotation whenever the modal closes */
                onClose: function(event) {
                    savedShapes[id].annotation = inputModal.getInputValue();
                }
            });

            inputModal.open();
        }

        var stopdrag = function(e) {
            $(this).off('mousemove');
            /* we're done drawing, save the shape */
            saveShape(e);
        };

        var startdrag = function() {
            $(this).on('mousemove', mousedrag);
        };

        /**
         * Calls the drawing function if the current mouse point
         * is atleast 6 pixels away from the last point (in either in the y or x direction).
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
                    // return out of this function if we do not have enough distance
                    return false;
                }
            }

            points.push({ x: Math.round(x), y: Math.round(y) });

            draw();

            return false;
        };

        /**
         * Takes the values from the points array,
         * and draws a line between each point.
         * This function is called from the mousedrag function.
         *
         * @return void
         */
        var draw = function() {
            /* clear the canvas each time */
            ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

            /* do not draw before we have atleast two points */
            if (points.length > 1) {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.3)';
                ctx.strokeStyle = 'rgb(255, 255, 255)';
                ctx.lineWidth = 2;

                ctx.beginPath();
                /* start the shape at the first coordinate in the array */
                ctx.moveTo(points[0].x, points[0].y);

                /* go through the array in in sequential order, drawing a line between each point */
                for (var i = 0; i < points.length; i++) {
                    if (points.length > 1) {
                        ctx.lineTo(points[i].x, points[i].y);
                    }
                }

                ctx.fill();
                ctx.stroke();
            }
        };

        /**
         * Draw the all the shapes in the savedShapes array.
         *
         * @return void
         */
		var drawSavedShapes = function() {
            /* clear the canvas each time */
            savedCtx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

            if (savedShapes.length > 0) {
                savedCtx.fillStyle = 'rgba(0, 100, 0, 0.6)';
                savedCtx.strokeStyle = 'rgba(0, 100, 0, 0.6)';
                savedCtx.lineWidth = 2;

                for (var k = 0; k < savedShapes.length; k++) {
                    for (var d = 0; d < savedShapes[k].fill.length; d++) {
                        savedCtx.fillRect(savedShapes[k].fill[d].x, savedShapes[k].fill[d].y, 1, 1);
                    }
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
            // $('#pan2').parent().append(
            //     '<i style="margin: 20px; position: absolute; top: 0; left: 0;" class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>'
            // );

            // only save the shape if we have atleast 3 points
            if (points.length > 2) {
                if (TOOL == "MARKER") {
                    // save all the x and y coordinates as well as any comment
                    savedShapes.push( new Shape(points, "") );
                    savedShapes[savedShapes.length-1].setFill();

                    if (savedShapes.length > 1) {
                        mergePossibleOverlapping();
                    }
                } else if (TOOL == "DELETE") {
                    deleteArea.push( new Shape(points, "") );
                    deleteArea[deleteArea.length-1].setFill();

                    // for each point in the delete shape look for a match in existing shapes
                    for (var i = 0; i < deleteArea[0].fill.length; i++) {
                        for (var j = 0; j < savedShapes.length; j++) {
                            for (var k = 0; k < savedShapes[j].fill.length; k++) {
                                /* if both X and Y matches in the savedShape and deleteArea array */
                                if ( savedShapes[j].fill[k].x == deleteArea[0].fill[i].x &&
                                     savedShapes[j].fill[k].y == deleteArea[0].fill[i].y ) {

                                    savedShapes[j].fill.splice(k, 1);

                                    break; /* we found a match, no need for more looping */
                                }

                            }
                        }
                    }

                    deleteArea = [];
                }
            }

            points = []; /* remove the current shape now that it's saved */

            draw();
            drawSavedShapes();

            // $('.fa-spinner').remove();
        };

        /**
         * Search for overlapping coordinates if we have more than one shape.
         *
         * @return void
         */
        var mergePossibleOverlapping = function() {
            var allShapesMerged = [];
            var duplicates = [];

            savedShapes.forEach(function(shape, i) {
                shape.fill.forEach(function(coord) {
                    allShapesMerged.push(coord);
                });
            });

            // group coordinates into arrays, matching coordinates gets put into the same array
            var groups = _.groupBy(allShapesMerged, function(item) {
                return [item.x, item.y];
            });

            // for each array: if the array contains more than one item,
            // we have a duplicate values. Push a of those values into an array which will effectively
            // hold a list which values that occure more than once.
            _.each(groups, function(group) {
                if (group.length > 1) {
                    duplicates.push.apply(duplicates, [group[0]]);
                }
            });

             for (var i = 0; i < duplicates.length; i++) {
                shapeLoop: for (var j = 0; j < savedShapes.length; j++) {
                    for (var k = 0; k < savedShapes[j].fill.length; k++) {

                        /* if both X and Y matches in the savedShape and deleteArea array */
                        if ( savedShapes[j].fill[k].x == duplicates[i].x &&
                             savedShapes[j].fill[k].y == duplicates[i].y ) {

                            savedShapes[j].fill.splice(k, 1);

                            break shapeLoop; /* we found a match, no need for more looping */
                        }
                    }
                }
            }

            duplicates = [];
            allShapesMerged = [];
        };

        /**
         * Delete one shape, by removing the last array element.
         * If no shapes are saved, empty the current drawn shape.
         *
         * @return void
         */
        var undo = function() {
            (points.length > 0) ? points = [] : savedShapes.pop();

            /* redraw to reflect the changes */
            draw();
            drawSavedShapes();
        };

        var reset = function() {
            points = [];
            savedShapes = [];

            /* redraw to reflect the changes */
            draw();
            drawSavedShapes();
        };



        /*---------------------------------------------
        		  Fill Algorithm for Polygon
        ---------------------------------------------*/


		/**
		 *  Remove duplicates from the matrix array.
		 *	Used for multidimensional arrays.
		 *	@param  {Array}	  The current matrix.
		 *	@return {Boolean} Returns an unique matrix.
		 */
		function removeDupeVerts(dataArray)
		{
			// This method is more effective rather than using For or For-each loops
			// URL: http://stackoverflow.com/questions/9229645/remove-duplicates-from-javascript-array
			// Answer by: georg | paragraph "Unique by..."
			// Fetched: 02.03.2016, 00:30.

			function uniqBy(a, key)
			{
				var seen = {};
				return a.filter(function(item)
				{
					var k = key(item);
					return seen.hasOwnProperty(k) ? false : (seen[k] = true);
				});
			}
			return uniqBy(dataArray, JSON.stringify);
		}

		/**
		 * Fill Algorithm | Ray-casting
		 * @param	{Array}	   Point(x,y) to check if it intersects with the polygon.
		 * @param	{Array}	   The rectangle area container for the polygon.
		 * @return 	{Boolean}  Returns true if the point is inside the polygon.
		 */
		function pointInsidePolygon(point, vertices)
		{
			// ray-casting algorithm based on
			// http://www.ecse.rpi.edu/Homepages/wrf/Research/Short_Notes/pnpoly.html

			var px = point[0], py = point[1];

			var inside = false;
			var j = vertices.length - 1;

			var xi, yi, xj, yj;

			for (var i = 0; i < vertices.length; i++)
			{
				var xi = vertices[i][0], yi = vertices[i][1];
				var xj = vertices[j][0], yj = vertices[j][1];

				var intersect = ((yi > py) != (yj > py))
					&& (px < (xj - xi) * (py - yi) / (yj - yi) + xi);

				if (intersect)
					inside = !inside;

				j = i;
			}
			return inside;
		}

		/**
		 * Calculate a rectangle of the polygon.
		 * Optimizing purposes to avoid iterating the whole image matrix for each polygon.
		 * @param  { Object }  Polygon object to calculate its rectangle.
		 * @return { Array }   The vertices of the calculated rectangle.
		 */
		function polygonToRectangle(polygon)
		{
			// Return lowest value in array:
			Array.min = function( array ){ return Math.min.apply( Math, array ); };

			// Return highest value in array:
			Array.max = function( array ){ return Math.max.apply( Math, array ); };

			var x = [], y = [];

			for(var i = 0; i < polygon.length; i ++)
			{
				x.push(polygon[i][0]);
				y.push(polygon[i][1]);
			}

			var rectVertices = [ Array.min(x), Array.min(y), Array.max(x), Array.max(y) ];

			return rectVertices;
		}

		/**
		 * Convert the polygon object vertices to array.
		 * @param  {Object}  polygon object.
		 * @return {Array}   polygon's vertices (x,y).
		 */
		function convertPolygonCoordToArray(polygon)
		{
			var array = [];
			var tempArr = [];
			for(var i = 0; i < polygon.points.length; i++)
			{
				tempArr = [polygon.points[i].x, polygon.points[i].y];
				array.push(tempArr);
			}
			return array;
		}

		/**
		 * Calculate total pixel points for all polygons.
		 * Estimates from the current savedShapes.
		 * @return {Array} Array of every marked pixel.
		 */
		var calcPolygonPoints = function()
		{
            // Atleast one polygon exists:
			if(savedShapes.length > 0)
			{
				var allMarkedPoints = []; // Store all marked pixels.

				for (var i = 0; i < savedShapes.length; i++)
				{
					for(var j = 0; j < savedShapes[i].fill.length; j ++)
					{
						allMarkedPoints.push([savedShapes[i].fill[j].x, savedShapes[i].fill[j].y] );
					}
				}

				var matrix = createMatrix(allMarkedPoints);	// Array with all matrixes and intersect value.

				return allMarkedPoints;
			}
		};

		/**
		 * Calculate all points inside a shape.
		 * @param  {Object}  A shape to find all marked pixels.
		 * @return {Array}   Array of every marked pixel for this shape.
		 */
		function calcFill(shape)
        {
			var t0 = performance.now();

			var polygonRect = [];									// Keeps the polygon rectangle vertices.
			var tempPolygonArr  = [];								// Keeps a polygon's coordinates in a 2D array.

			var fillArray = [];
            tempPolygonArr = convertPolygonCoordToArray(shape); 	// Convert to array.
            polygonRect = polygonToRectangle(tempPolygonArr);		// Return array of vertices from the polygon's rectangle.
                                                                    // All 4 vertices of the rectangle:
            var rect_p1 = polygonRect[0], rect_p2 = polygonRect[1],
                rect_p3 = polygonRect[2], rect_p4 = polygonRect[3];

			for(var i = 0; i <= image.width; i ++ )
            {
                for(var j = 0; j <= image.height; j++)
                {
                    var point = [i,j];          						// Check this point.

                    if( pointInsidePolygon(point, tempPolygonArr) ) {	// The point is inside the polygon:
                        fillArray.push( {x:i, y:j} );
                    }
                }
            }

			var t1 = performance.now();

            return fillArray;
        }


/*---------------------------------------------------------------------------
							END: Fill Algorithm for Polygon
-----------------------------------------------------------------------------*/


    }; // end of initCanvasMarkingTool()

})(jQuery);
