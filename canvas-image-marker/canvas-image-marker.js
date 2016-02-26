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
            canvas.on('mousedown', click);

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
/*---------------------------------------------------------------------------	
							Fill Algorithm for Polygon
-----------------------------------------------------------------------------*/
		
		
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
		
		function drawMatrix(matrixData)
		{
			//console.log(matrixData);
			
			matrixCtx.fillStyle = "#fff";
			
			for(var i = 0; i < matrixData.length; i++)
			{
			
				matrixCtx.fillRect( matrixData[i][0], matrixData[i][1], 1, 1 );
				
			}
			
			
	
				
		
		}
		
		/**
		 * Fill Algorithm | Ray-casting
		 * @param	{Array}		Takes a single point(x,y) to 
		 *						check if it intersects with the polygon.
		 * @param	{Array}		To check if the point intersects within the 
		 * 						boundaries of the polygon or not.	
		 * @return 	{Boolean} 	Whether the point intersects or not.
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
		 * Calculate the vertices of the polygon's rectangle.
		 * Optimizing purposes to avoid iterating the whole image matrix for each polygon.
		 * @param  { Object } Polygon object to be calculated into vertices.
		 * @return { Array }  The vertices of the polygon's rectangle.
		 */
		function getRectVerticesPolygon(polygon)
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
		 * @param  {Object} polygon object.
		 * @return {Array}  polygon's vertices (x,y).
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
			/* var test = confirm("Ok: Computed test (this will clear your polygons) \nCancel: Your polygons ");
			
			if(test)
				testData(); */
			
			if(savedShapes.length > 0)
			{
			
				var lol = confirm("Do you want to optimize the data?");
				var t0 = performance.now();
				
				var allMarkedPoints = [];
				var tempPolygonArr = [];
				
				for (var i = 0; i < savedShapes.length; i++)
				{
					tempPolygonArr = convertPolygonCoordToArray(savedShapes[i]); // Convert to array.
					
					if(lol)
					{
						var rectVertices = getRectVerticesPolygon(tempPolygonArr);	// return array of vertices.
						//console.log(rectVertices);
						
						var xmin = rectVertices[0], ymin = rectVertices[1],
							xmax = rectVertices[2], ymax = rectVertices[3];
						
						for(var j = xmin; j < xmax; j ++ )
						{
							for(var k = ymin; k < ymax; k++)
							{
								var point = [j,k];
								if( pointInsidePolygon(point, tempPolygonArr) )
									allMarkedPoints.push(point);
							}
						} 
					}
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
				
				
				
				var t1 = performance.now();
				console.log("Fill polygon took " + (t1 - t0) + " milliseconds.");
				
				console.log('Vertices: ' + allMarkedPoints.length);
				
				if( allMarkedPoints.length > 500000 )
					alert(' OVER 500 000 PUNKTER!? :O \n VI HAR ET KJEMPEPROBLEM!!!');
				
				//return allMarkedPoints;
				
				drawMatrix(allMarkedPoints);
			}
			else
				alert('create a polygon or run a computed test');
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
