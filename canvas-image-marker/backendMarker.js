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
        }, options);

        // get the current database id of the experiment
        var experimentID = $(this).attr('data-experimentId');
        // get the current database id of the image
        var pictureID = $(this).attr('data-picture-id');

        // canvas where the drawing will take place
        var canvas = $('<canvas>');
        var ctx = canvas[0].getContext('2d');

        // put our savedShapes here, so we don't have to redraw them every time something changes
        var savedCanvas = $('<canvas>');
		var savedCtx = savedCanvas[0].getContext('2d');

        var matrixCanvas = $('<canvas>');
        var matrixCtx = matrixCanvas[0].getContext('2d');

        var canvasContainer = element;
        var image;

        var points = [];
        var savedShapes = [];
        var deleteArea = [];

		var savedData = false;	// temp bool for saving in user testing.


        $(document).ready(function() {
            setCanvasImage();
            $(canvasContainer).append(savedCanvas); // append the resized canvas to the DOM
            $(canvasContainer).append(canvas); // append the resized canvas to the DOM
            $(canvasContainer).after(matrixCanvas); // append the resized canvas to the DOM
        });

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
                matrixCanvas.attr('height', image.height).attr('width', image.width);
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

            matrixCanvas.css({
                background: "#111"
            });
        };


        /*--------------------------------------------------------*
         *				Fill Algorithm for Polygon                *
         *--------------------------------------------------------*/

		$('#hueLevel').change(function() { $(this).next().text( $(this).val() ); });
		$('#satLevel').change(function() { $(this).next().text( $(this).val() ); });


		/**
		 *  Create a matrix of the experiment image with marked points as data.
		 *	@param  {array}  The array with marked points.
		 *	@return {array}	 The matrix.
		 */
		function createMatrix(data)
		{
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

			// Calc matrix: Very good Performance:
			for(var i = 0; i < data.length; i++)
			{
				matrix[ data[i][0] ][ data[i][1] ].val++;
			}

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

				var allMarkedPoints = [];											// Store all marked pixels.

				for (var i = 0; i < savedShapes.length; i++)
				{
					for(var j = 0; j < savedShapes[i].fill.length; j ++)
					{
						allMarkedPoints.push([savedShapes[i].fill[j].x, savedShapes[i].fill[j].y] );
					}
				}

				if( $('#hueScale[type=checkbox]').is(':checked') )
					scale = 1;


				var matrix = createMatrix(allMarkedPoints);							// Array with all matrixes and intersect value.
				drawMatrixCanvas(matrix, hue, sat, scale);

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



    }; // end of initCanvasMarkingTool()

})(jQuery);
