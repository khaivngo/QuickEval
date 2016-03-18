/**
 *	Generate Heatmap
 */


var savedShapes;

var image = new Object();
image.width = 500;
image.height = 300;

var matrixCanvas = $('<canvas>');
var matrixCtx = matrixCanvas[0].getContext('2d');

matrixCanvas.attr('height', image.height).attr('width', image.width);
matrixCanvas.css('background','url(free1.jpg)' );

$(document).ready(function()
{
	$('#heatmapCanvasContainer').append(matrixCanvas);
	$.ajax
	({
		url: 'fetchShapesFromDb.php'
	})
	.done(function(data)
	{
		var shapes = JSON.parse(data);
		
		// Parse the fill array with its objects
		for(var i = 0; i < shapes.length; i++)
		{
			shapes[i].fill = JSON.parse(shapes[i].fill);
		}
		savedShapes = shapes;
		calcPolygonPoints();	
	});
	$('#genHeatmap').on('click',function(){calcPolygonPoints();});
	$('#hueLevel').change(function() { $(this).next().text( $(this).val() ); });
	$('#satLevel').change(function() { $(this).next().text( $(this).val() ); });
});

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
			$('#heatmap-panel-container').prepend(htmlScale);
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
 
/**
 * Calculate total pixel points for all polygons.
 * Estimates from the current savedShapes.
 * @return {Array} Array of every marked pixel.
 */
function calcPolygonPoints()
{
	//console.log(savedShapes);
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
	console.log("Render Heatmap took total " + Math.round(t1 - t0) / 1000 + " seconds. \n\n");

	//return allMarkedPoints;

}