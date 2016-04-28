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
            annotation: false
        }, options);

        /* get the current database id of the experiment */
        var experimentID = $(this).attr('data-experimentId');
        /* get the current database id of the image */
        var pictureID = $(this).attr('data-picture-id');
        /* get the picture queue */
        var pictureQueue = $(this).attr('data-picture-queue');

        /* save a reference to this canvas container element, so we can still manipulate it later */
        var self = $(this);

        // listen for updates on the data-picture-queue attribute on the .canvas-container element
        // so we can update the variable in here
        // also run the setCanvasImage function to update the image
        $(document).on('data-attribute-changed', function() {
            pictureQueue = self.attr('data-picture-queue');
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
                    '<button title="Enable marking tool" style="font-size: 25px;" class="tool-button enable-marking marking-tool"><i class="fa fa-pencil-square-o"></i></button>' +
                    '<div class="mode marking-tools">' +
                        '<button class="tool-button draw-tool down-arrow" style="background: #eee;"><i class="fa fa-pencil"></i></button>' +
                        '<button class="tool-button erase-tool"><i class="fa fa-scissors"></i></button>' +
                        '<button class="tool-button undo"><i class="fa fa-undo"></i></button>' +
                    '</div>' +
                '</div>' +
            '</div>');

        var canvasContainer = element;
        var image;

        var points = [];
        var savedShapes = [];
        var deleteArea = [];

		var savedData = false;	// temp bool for saving in user testing.

        var TOOL = "MARKER"; /* keeps track of whether the users is drawing or erasing */

        if (settings.annotation) {
            var Remark = new Annotation();
            // append a annotationBox to the canvasContainer element
            Remark.createAnnotation(canvasContainer, canvasIndex);

            Remark.annotationSaveButton.on('click', function(e) {
                var shapeID = Remark.annotationBox.attr('data-id'); // id of clicked shape
                var annoText = $.trim(Remark.annotationTextarea.val()); // text of clicked shape

                // update the annotation property of the shape
                savedShapes[shapeID].annotation = annoText;

                Remark.closeAnnotation(e);
            });
        }

        $(document).ready(function() {
            setCanvasImage();
            $(canvasContainer).append(savedCanvas); // append the resized canvas to the DOM
            $(canvasContainer).append(canvas); // append the resized canvas to the DOM

            if (settings.showToolbar == true) {
                $(canvasContainer).parent().parent().parent().prepend(toolPanel);
            }

            toolPanel.find('.undo').on('click', undo);
            toolPanel.find('.marking-tool').on('click', setMarkingActiveTool);
            toolPanel.find('.draw-tool').on('click', setMarkingActiveTool);
            toolPanel.find('.erase-tool').on('click', setEraseActiveTool);
            toolPanel.find('.enable-marking').on('click', setModeToPanning);
            toolPanel.find('.enable-panzoom').on('click', setModeToDrawing);

            toolPanel.find('.marking-tools button').on('click', function() {
                toolPanel.find('.marking-tools button').removeClass('down-arrow');
                toolPanel.find('.marking-tools button').css('background', '#ccc');

                $(this).css('background', '#eee');
                $(this).addClass('down-arrow');
            });

            $('#button-finished').on('click', sendMarkToDB);
        });

        var sendMarkToDB = function() {
            savedShapes.forEach(function(shape) {
                var fillAsJSONstring = JSON.stringify(shape.fill);
                $.ajax({
                    url: 'ajax/observer/experimentMarks.php',
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
                .done(function(response) {
                    console.log(response);
                })
                .fail(function(response) {
                    console.log(response.responseText);
                });
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

		var Shape = function(points, annotation)
        {
            this.points = points;
            this.annotation = annotation;
            this.fill;

            this.setFill = function()
            {
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
                    Remark.openAnnotation(mouseY, mouseX, k, canvasIndex, savedShapes[k].annotation);
                    break; /* we found the clicked polygon, no need to loop through the rest */
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
            // only save the shape if we have atleast 3 points
            if (points.length > 2) {
                if (TOOL == "MARKER") {
                    // save all the x and y coordinates as well as any comment
                    savedShapes.push( new Shape(points, " ") );
                    savedShapes[savedShapes.length-1].setFill();
                } else if (TOOL == "DELETE") {
                    deleteArea.push( new Shape(points, " ") );
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
        };

        /**
         * Delete one shape, by removing the last array element.
         * If no shapes are saved, empty the current drawn shape.
         *
         * @return void
         */
        var undo = function() {
            (points.length > 0) ? points = [] : savedShapes.pop();

            draw();
            drawSavedShapes();
        };

        var reset = function() {
            points = [];
            savedShapes = [];

            draw();
            drawSavedShapes();
        };



        /*---------------------------------------------
        		  Fill Algorithm for Polygon
        ---------------------------------------------*/

		$('#hueLevel').change(function() { $(this).next().text( $(this).val() ); });
		$('#satLevel').change(function() { $(this).next().text( $(this).val() ); });

		/**
		 *  Create a matrix of the experiment image with marked points as data.
		 *	@param  {array}  The array with marked points.
		 *	@return {array}	 The matrix.
		 */
		function createMatrix(data)
		{
			var t0 = performance.now();
			var matrix = [];

			// Init matrix: Very good performance:
			for (var i = 0; i < image.width; i++)
			{
				matrix[i] = [];
				for (var j = 0; j < image.height; j++)
				{
					matrix[i][j] = {val: 0};
				}
			}

			var t1 = performance.now();
			console.log('Init matrix:' + Math.round(t1 - t0) / 1000 + ' seconds.');

			var t2 = performance.now();

			// Calc matrix: Very good Performance:
			for(var i = 0; i < data.length; i++)
			{
				matrix[ data[i][0] ][ data[i][1] ].val++;
			}

			var t3 = performance.now();
			console.log('Calc matrix:' + Math.round(t3 - t2) / 1000 + ' seconds.');

			return matrix;
		}

		/**
		 *  Generate color for the heatmap.
		 *	@param  {Int}	  The current intersection value for the pixel.
		 *	@param  {Int}	  The highest value of intersections.
		 *	@return {String}  color in hsl format.
		 */
		function heatmapColor(cur, max, scaleType, huePassed, satPassed)
		{
			var valPerc = ( (cur-1) / (max-1) );
			var color;

			function hueScale()
			{
				var hueLow = 125;						// Green value in hue scale
				var hue = hueLow - (hueLow * valPerc);
				color = 'hsl(' + hue + ',50%,50%)';

				return color;
			}

			function grayScale(hue, sat)
			{
				var light = valPerc * 100;
				var hueStd = 0;

				var color = 'hsl(' + hue + ',' + sat + '%,' + light + '%)';

				return color;
			}

			switch(scaleType)
			{
				case 0: return grayScale(huePassed, satPassed);
				case 1: return hueScale();
			}

		}

		/**
		 * Render the legend scale for the heatmap.
		 *	@param  {Int}	  The scale type, 0: monochromatic, 1: hsl.
		 *	@param  {Int}	  The hue value.
		 *	@param  {Int}	  The saturation value.
		 *  @return {Void}
		 */
		function renderHeatmapLegend(scaleType, hue, sat)
		{
			var colorStep = 0;
			var RANGE = 10;

			var htmlScale = "";

			$('#heatmapLegend').remove();

			switch (scaleType)
			{
				case 0:
					colorStep = 100 / RANGE;

					htmlScale += '<div id = "heatmapLegend">';

					for(var i = 0; i < RANGE; i++)
					{
						var color = 'hsl(' + hue + ',' + sat + '%,' + colorStep * i + '%)';
						htmlScale += '<div class = "scaleItem" style = "background-color:'+color+'"></div>';
					}

					htmlScale += '</div>'
					$('body').append(htmlScale);
					break;

				case 1:
					colorStep = 125 / RANGE;

					htmlScale += '<div id = "heatmapLegend">';

					for(var i = RANGE-1; i >= 0; i--)
					{
						var color = 'hsl(' + colorStep * i + ',50%, 50%)';
						htmlScale += '<div class = "scaleItem" style = "background-color:'+color+'"></div>';
					}

					htmlScale += '</div>'
					$('body').append(htmlScale);
					break;
			}
		}

		/**
		 *  Draw the matrix in canvas as heatmap.
		 *	@param  {array}	  The matrix data to draw.
		 *	@param  {Int}	  The hue value for the heatmap.
		 *	@param  {Int}	  The saturation value for the heatmap.
		 *	@param  {Int}	  The scale type, 0: grayscale, 1: hsl.
		 *	@return {Void}.
		 */
		function drawMatrixCanvas(data, hue, sat, scaleType)
		{
			var t0 = performance.now();
			var maxVal = 0;

			// Get max intersection value:
			for(var i = 0; i < data.length; i ++)
			{
				for(var j= 0; j < data[i].length; j ++)
				{
					if(i == 0)
						maxVal = data[i][j].val;
					else
					{
						if(data[i][j].val > maxVal)
							maxVal = data[i][j].val;
					}
				}
			}

			// Draw matrix with heatmap:
			for(var i = 0; i < data.length; i++)
			{
				for(var j= 0; j < data[i].length; j ++)
				{
					if(data[i][j].val > 0)
					{
						var point = [i,j];
						matrixCtx.fillStyle = heatmapColor(data[i][j].val, maxVal, scaleType, hue, sat);
						matrixCtx.fillRect( point[0], point[1], 1, 1 );
					}
				}
			}

			renderHeatmapLegend(scaleType, hue, sat);

			var t1 = performance.now();
			console.log('Render matrix:' + Math.round(t1 - t0) / 1000 + ' seconds.');
		}

		// Render matrix in html table,
		// (!) not finished.
		function drawMatrixTable(matrixData)
		{
			$('body').remove('#matrixTable');
			$('body').append('<table id = "matrixTable" style = "font-size: 25%;"></table>');

			var table = $('#matrixTable');

			var tableData = "";

			var flag;

			for(var i = 0; i < matrixData.length-1; i++ )
			{
				if( i % 800 == 0 )
				{
					flag = i;
					tableData += "<tr>";
				}

				if(matrixData[i].val > 0)
					tableData += '<td style = "color: #fff;">'+matrixData[i].val+'</td>';
				else
					tableData += '<td>0</td>';

				if ( i != flag && i % 800 == 0 )
					tableData += "</tr>";
			}

			table.html(tableData);
		}

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
			if(savedShapes.length > 0) 												// Atleast one polygon exists:
			{
				var hue = $('#hueLevel').val();
				var sat = $('#satLevel').val();
				var scale = 0;

				var t0 = performance.now();
				var allMarkedPoints = [];											// Store all marked pixels.

				for (var i = 0; i < savedShapes.length; i++)
				{
					for(var j = 0; j < savedShapes[i].fill.length; j ++)
					{
						allMarkedPoints.push([savedShapes[i].fill[j].x, savedShapes[i].fill[j].y] );
					}
				}

				//console.log('Before removing dupes: ' + allMarkedPoints.length);
				//allMarkedPoints = removeDupeVerts(allMarkedPoints);					// Remove duplicated vertices.
				//console.log('After removing dupes: ' + allMarkedPoints.length);

				if( $('#hueScale[type=checkbox]').is(':checked') )
					scale = 1;


				var matrix = createMatrix(allMarkedPoints);							// Array with all matrixes and intersect value.
				drawMatrixCanvas(matrix, hue, sat, scale);

				//drawMatrixTable(matrix);

				var t1 = performance.now();
				console.log("Fill polygon took " + Math.round(t1 - t0) / 1000 + " seconds. \n\n");

				return allMarkedPoints;
			}
			else
				alert('Please create a polygon');
		};

		/**
		 * Calculate all points inside a shape.
		 * @param  {Object}  A shape to find all marked pixels.
		 * @return {Array}   Array of every marked pixel for this shape.
		 */
		function calcFill(shape)
        {
			var polygonRect = [];									// Keeps the polygon rectangle vertices.
			var tempPolygonArr  = [];								// Keeps a polygon's coordinates in a 2D array.

			var fillArray = [];
            tempPolygonArr = convertPolygonCoordToArray(shape); 	// Convert to array.
            polygonRect = polygonToRectangle(tempPolygonArr);		// Return array of vertices from the polygon's rectangle.
                                                                    // All 4 vertices of the rectangle:
            var rect_p1 = polygonRect[0], rect_p2 = polygonRect[1],
                rect_p3 = polygonRect[2], rect_p4 = polygonRect[3];

                                                                        // Find all the points that are inside the polygon:
            for(var i = rect_p1; i < rect_p3; i ++ )
            {
                for(var j = rect_p2; j < rect_p4; j++)
                {
                    var point = [i,j];          						// Check this point.

                    if( pointInsidePolygon(point, tempPolygonArr) ) {	// The point is inside the polygon:
                        fillArray.push( {x:i, y:j} );
                    }
                }
            }

            return fillArray;
        }


/*---------------------------------------------------------------------------
							END: Fill Algorithm for Polygon
-----------------------------------------------------------------------------*/


    }; // end of initCanvasMarkingTool()

})(jQuery);
