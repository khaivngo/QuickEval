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

		var matrixCanvas = document.getElementById('matrix');
		var matrixCtx = matrixCanvas.getContext('2d');


        $(document).ready( function() {
            setCanvasImage();

            $(canvasContainer).append(canvas); /* append the resized canvas to the DOM */
            canvas.on('mousedown', startdrag);
            canvas.on('mouseup', stopdrag);
            canvas.on('dblclick', click);

            $('#undo').on('click', undo);
            $('#reset').on('click', reset);
            $('#fillAlg').on('click', calcPolygonPoints);
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

            drawSavedShapesEnd();

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
                ctx.strokeStyle = 'rgba(0, 100, 0, 0.3)';
                ctx.lineWidth = 2;

                for (var k = 0; k < savedShapes.length; k++) {
                    ctx.beginPath();
                    ctx.moveTo(savedShapes[k].points.x, savedShapes[k].points.y);

                    for (var d = 0; d < savedShapes[k].points.length; d++) {
                        ctx.lineTo(savedShapes[k].points[d].x, savedShapes[k].points[d].y);
                    }
                    ctx.closePath();
                    ctx.fill();
                    // ctx.stroke();
                }
            }
        };

        var drawSavedShapesEnd = function() {
            if (savedShapes.length > 0) {
                ctx.fillStyle = 'rgba(0, 100, 0, 0.3)';
                ctx.strokeStyle = 'rgba(0, 100, 0, 0.3)';
                ctx.lineWidth = 2;

                for (var k = 0; k < savedShapes.length; k++) {
                    // ctx.beginPath();
                    // ctx.moveTo(savedShapes[k].fill.x, savedShapes[k].fill.y);

                    for (var d = 0; d < savedShapes[k].fill.length; d++) {
                        ctx.fillRect(savedShapes[k].fill[d].x, savedShapes[k].fill[d].y, 1, 1);
                    }
                    // ctx.closePath();
                    // ctx.fill();
                    // ctx.stroke();
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
                savedShapes.push( new Shape(points, "") );

                savedShapes[savedShapes.length-1].setFill();
            }

            points = []; /* remove the current shape now that it's saved */

            draw();
            drawSavedShapesEnd();
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
/*---------------------------------------------------------------------------
							Fill Algorithm for Polygon
-----------------------------------------------------------------------------*/

		function writeToFile(arr)
		{
			var array = JSON.stringify(arr);

			$.ajax
			({
				url: "writeDataToFile.php",
				data: { array: array },
				type: "POST"
			})
			.done(function(data)
			{
				alert(data);
			});
		}


		// Temporary function for testing.
		function testData()
		{
			savedShapes = []; 	//reset.
			var a = [];
			for (var i = 0; i < 500; i ++)
			{
				a.push({x: i+50, y: i+20 })
			}

			savedShapes.push({ points: a, annotation: "" });
			console.log(savedShapes);
		}

		// Draw matrix in canvas.
		function drawMatrixCanvas()
		{
			//console.log(matrixData);

			matrixCtx.fillStyle = "#fff";

			for(var i = 0; i < savedShapes.length; i++)
			{
                for(var j = 0; j < savedShapes.length; j ++)
				matrixCtx.fillRect( savedShapes[i].fill[j].x, savedShapes[i].fill[j].y, 1, 1 );
			}
		}

		// Render matrix in html table,
		// (!) not finished.
		function drawMatrixTable(matrixData)
		{
			$('body').append('<table id = "matrixTable"></table>');

			var table = $('#matrixTable');

			for(var i = 0; i < 800; i ++ )
			{
				table.append("<tr>");

				for(var j = 0; j < 533; j ++)
				{

					//table.find("tr").eq(i).append("<td>0</td>");

				}
				table.append("</tr>");
			}

			console.log('finished');
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
			// Answear by: georg | paragraph "Unique by..."
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
		 * @param	{Array}		Point(x,y) to check if it intersects with the polygon.
		 * @param	{Array}		The rectangle area container for the polygon.
		 * @return 	{Boolean} 	Returns true if the point is inside the polygon.
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
		 * @param  { Object }	Polygon object to calculate its rectangle.
		 * @return { Array }  	The vertices of the calculated rectangle.
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
		 * @param  {Object} 	polygon object.
		 * @return {Array}  	polygon's vertices (x,y).
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
		 * @return { Array } Array of every marked pixel.
		 */
		var calcPolygonPoints = function()
		{
			/* NOTICE:
				Ikke bry dere om variabler som har TEMP foran seg */


			/* TEMP: */
			/* var test = confirm("Ok: Computed test (this will clear your polygons) \nCancel: Your polygons ");
				if(test)
					testData();
			*/

			if(savedShapes.length > 0) 												// Atleast one polygon exists:
			{
				/* TEMP */ var optimizeData = confirm("Do you want to optimize the data?");
				/* TEMP */ var t0 = performance.now();

				var allMarkedPoints = [];											// Store all marked pixels.
				var tempPolygonArr  = [];											// Keeps a polygon's coordinates in a 2D array.
				var polygonRect = [];												// Keeps the polygon rectangle vertices.

				for (var i = 0; i < savedShapes.length; i++)
				{
					tempPolygonArr = convertPolygonCoordToArray(savedShapes[i]); 	// Convert to array.

					if(optimizeData) 												// Bare for testing, Fokuser på denne if løkka, drit i else løkka.
					{
						polygonRect = polygonToRectangle(tempPolygonArr);			// Return array of vertices from the polygon's rectangle.

																					// All 4 vertices of the rectangle:
						var rect_p1 = polygonRect[0], rect_p2 = polygonRect[1],
							rect_p3 = polygonRect[2], rect_p4 = polygonRect[3];

																					// Find all the points that are inside the polygon:
						for(var j = rect_p1; j < rect_p3; j ++ )
						{
							for(var k = rect_p2; k < rect_p4; k++)
							{
								var point = [j,k]; 									// Check this point.

								if( pointInsidePolygon(point, tempPolygonArr) ) 	// The point is inside the polygon:
									allMarkedPoints.push(point);
							}
						}
					}
					// Skal fjernes:
					else
					{
						for(var j = 0; j < 800; j ++ )
						{
							for(var k = 0; k < 533; k++)
							{
								var point = [j,k];
								if( pointInsidePolygon(point, tempPolygonArr) )
									allMarkedPoints.push(point);
							}
						}
					}

				}
				console.log('Before removing dupes: ' + allMarkedPoints.length);

				allMarkedPoints = removeDupeVerts(allMarkedPoints);					// Remove duplicated vertices.

				console.log('After removing dupes: ' + allMarkedPoints.length);

				if( allMarkedPoints.length > 500000 )
					alert(' OVER 500 000 PUNKTER!? :O \n VI HAR ET KJEMPEPROBLEM!!!');

				drawMatrixCanvas(allMarkedPoints);
				//drawMatrixTable(allMarkedPoints);


				/* TEMP */ //writeToFile(allMarkedPoints);

				/* TEMP */var t1 = performance.now();
				/* TEMP */console.log("Fill polygon took " + (t1 - t0) + " milliseconds.");

				//return allMarkedPoints;
			}
			else
				alert('create a polygon or run a computed test');
		}

        /*----------------------*/

        function calcFill(shape)
        {
            fillArray = [];
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
                    var point = [i,j]; 									// Check this point.

                    if( pointInsidePolygon(point, tempPolygonArr) ) 	// The point is inside the polygon:
                        fillArray.push( {x:i, y:j} )
                }
            }

            return fillArray;

        }

/*---------------------------------------------------------------------------
							END: Fill Algorithm for Polygon
-----------------------------------------------------------------------------*/
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
