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
            imageUrl: $(this).attr('data-image-url')
        }, options);

        var HUE_LOW = 240;		// For heatmap lowest value in Jet scale.

        var heatmapLegend = new Object();
        heatmapLegend.height = 60;							// Extended value for heatmap legend scale in canvas.
        heatmapLegend.square = heatmapLegend.height / 2;	// Height/width for each square box in the heatmap legend.
        heatmapLegend.marginTop = 5;						// Remember to consider the heatmapLegend.height value when assigning new value.
        heatmapLegend.marginLeft = 75;						// Remember to consider the image.width value when assigning new value.
        heatmapLegend.paddingLeft = 4;						// Padding left for each square box.


        // Canvas base image:
        var imageCanvas = $('<canvas>');
        var imageCtx = imageCanvas[0].getContext('2d');

        // Canvas where the drawing will take place:
        var matrixCanvas = $('<canvas>');
        var matrixCtx = matrixCanvas[0].getContext('2d');

        // Merged canvas from image and marking:
        var mergedCanvas = $('<canvas>');
        var mergedCtx = mergedCanvas[0].getContext('2d');

        var canvasContainer = element;
        var image;

        var savedShapes;

        $(document).ready(function() {
            setCanvasImage();
            setImage();

            $(canvasContainer).append(imageCanvas); // append the resized canvas to the DOM
            $(canvasContainer).append(matrixCanvas); // append the resized canvas to the DOM
            $(canvasContainer).append(mergedCanvas); // append the resized canvas to the DOM

            getArtifactMarks(settings.experimentID, settings.pictureQueue, settings.pictureID);
        });

        /**
         * Figure out the size of the image, so we can set the canvas to the same size.
         */
        function setCanvasImage() {
            image = new Image();

            var resize = function() {
                // make all elements the same size as the image
                $(canvasContainer).css({
                    height: image.height + heatmapLegend.height,
                    width: image.width,
                    background: "#ccc"
                });

                imageCanvas.attr('height', image.height + heatmapLegend.height).attr('width', image.width).attr('id','heatmapCanvasImage');
                matrixCanvas.attr('height', image.height + heatmapLegend.height).attr('width', image.width).attr('id','heatmapCanvasMatrix');
                mergedCanvas.attr('height', image.height + heatmapLegend.height).attr('width', image.width).attr('id','heatmapCanvasMerged');
            };

            $(image).load(resize);
            image.src = settings.imageUrl;

            if (image.loaded)
                resize();

            imageCanvas.css({
                background: 'url(' + image.src + ') no-repeat',
                position: "absolute", top: 0, right: 0
            });

            matrixCanvas.css({
                position: "absolute", top: 0, right: 0
            });

            mergedCanvas.css({
                position: "absolute", top: 0, right: 0
            });
        }

         /**
          * Get all artifact marks for a specific image.
          */
         function getArtifactMarks(experimentID, pictureQueue, pictureID) {
             $.ajax({
                 url: 'ajax/scientist/getExperimentArtifactMarks.php',
                 type: 'POST',
                 data: {
                     experiment_id: experimentID,
                     picture_id: pictureID,
                     picture_queue: pictureQueue
                 },
                 dataType: 'json',
                 encode: true,
                 cache: false
             })
             .done(function(data) {
                if (data.length > 0) {

                    var shapes = [];

                    for (var i = 0; i < data.length; i++) {
                        shapes.push({ fill: JSON.parse(data[i].marked_pixels) });
                    }

                    savedShapes = shapes;

                    calcPolygonPoints();
                }
             });
         }


         function exportHeatmap(experimentID, pictureQueue, pictureID) {
             $.ajax({
                 url: 'ajax/scientist/getExperimentArtifactMarks.php',
                 type: 'POST',
                 data: {
                     experiment_id: experimentID,
                     picture_id: pictureID,
                     picture_queue: pictureQueue
                 },
                 dataType: 'json',
                 encode: true,
                 cache: false
             })
             .done(function(data) {
                console.log('sent');
             });
         }


        /**
         * Draws the image on the image canvas for when the image is downloaded
         * (simply having the image as a background with CSS won't work).
         */
        function setImage() {
            var base_image = new Image();
            base_image.src = settings.imageUrl;
            base_image.onload = function() {
                imageCtx.drawImage(base_image, 0, 0);
            }
        }


        /*---------------------------------------
         		Fill Algorithm for Polygon
         ---------------------------------------*/

         $(document).ready(function()
         {
         	// Generate heatmap button.
         	$('#genHeatmap').on('click', calcPolygonPoints);

         	/**
         	 *  UI for heatmap generator.
         	 *	When the user changes the Sliders range, either hue or saturation.
         	 *  @return {void}.
         	 */
         	$('#heatmapPanel li input[type=range]').on('input',function(event)
            {
         		var hue = $('#hueLevel').val();		// Get the current hue level.
         		var sat = $('#satLevel').val();		// Get the current saturation level.

         		$(this).parent().parent().find('.sizeNumber').val( $(this).val() ); // Change current label text value for this slider.

         		setSliderColor(hue, sat);

         		if( $('#liveGen[type=checkbox]').is(':checked') )
         			calcPolygonPoints();
         	});

         	$('#heatmapPanel li input[type=number]').on('input',function(event)
         	{
         		var hue = $('#hueSection input[type=number]').val();
         		var sat = $('#satSection input[type=number]').val();

         		$('#hueLevel').val(hue);
         		$('#satLevel').val(sat);

         		setSliderColor(hue, sat);

         		if( $('#liveGen[type=checkbox]').is(':checked') )
         			calcPolygonPoints();
         	});

         	$('#jetScale[type=checkbox]').on('change',function(event)
         	{
         		var range = document.getElementById("hueLevel");

         		if( $(this).is(':checked') )
         		{
         			range.disabled = true;
         			$('#hueSection').attr('class','inactiveSection');
         		}
         		else
         		{
         			range.disabled = false;
         			$('#hueSection').attr('class','activeSection');
         		}
         	});

         	$('#liveGen[type=checkbox]').on('change',function(event)
         	{
         		var genButton = document.getElementById("genHeatmap");

         		if( $(this).is(':checked') )
         		{
         			genButton.disabled = true;
         			$('#genHeatmap').attr('class','inactiveSection');
         		}
         		else
         		{
         			genButton.disabled = false;
         			$('#genHeatmap').attr('class','activeSection');
         		}
         	});


         	document.getElementById('downloadImage').addEventListener('click', function()
         	{
         		downloadCanvas(this, 'heatmapCanvasMerged', 'test.png');
         	}, false);

         });



         function downloadCanvas(link, canvasId, filename)
         {
         	var imageCanvasTemp = document.getElementById('heatmapCanvasImage');
         	var matrixCanvasTemp = document.getElementById('heatmapCanvasMatrix');

         	mergedCtx.drawImage(imageCanvasTemp, 0, 0);
         	mergedCtx.drawImage(matrixCanvasTemp, 0, 0);

         	link.href = document.getElementById(canvasId).toDataURL();
         	link.download = filename;

         	// Reset mergedCtx for further manipulation
         	mergedCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);
         }

         function setSliderColor(hue, sat)
         {
         	for(var i = 0; i < document.styleSheets[4].rules.length; i++)
         	{
         		var rule = document.styleSheets[4].rules[i];
         		if(rule.cssText.match('::-webkit-slider-thumb'))
         			rule.style.backgroundColor = 'hsl('+hue+','+sat+'%,50%)';
         	}
         }

         /**
          * Create a matrix of the experiment image with marked points as data.
          *
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
          * Generate color for the heatmap.
          *	@param  {Int}	  The current intersection value for the pixel.
          *	@param  {Int}	  The highest value of intersections.
          *	@return {String}  color in hsl format.
          */
         function jetScale(valPerc, sat)
         {
         	var hue = HUE_LOW - (HUE_LOW * valPerc);
         	var color = 'hsl(' + hue + ',' + sat + '%,50%)';
         	return color;
         }

         function grayScale(valPerc, hue, sat)
         {
         	var light = valPerc * 100;
         	var color = 'hsl(' + hue + ',' + sat + '%,' + light + '%)';
         	return color;
         }

         /**
          * Generate color for the heatmap.
          *	@param  {Int}	  The current intersection value for the pixel.
          *	@param  {Int}	  The highest value of intersections.
          *	@return {String}  color in hsl format.
          */
         function heatmapColor(cur, max, scaleType, hue, sat, reverse)
         {
         	var valPerc = (cur-1) / (max-1);

         	if(reverse)
         		valPerc = 1 - valPerc;

         	switch(scaleType)
         	{
         		case 0: return grayScale(valPerc, hue, sat);
         		case 1: return jetScale(valPerc, sat);
         	}
         }
         /**
          * Render the legend scale for the heatmap.
          *	@param  {Int}	  The scale type, 0: monochromatic, 1: jet.
          *	@param  {Int}	  The hue value.
          *	@param  {Int}	  The saturation value.
          * @return {Void}
          */
         function renderHeatmapLegend(scaleType, maxVal, hue, sat, reverse)
         {
         	var colorStep = 0;
         	var deltaPadding = 0;
         	var index = 0;
         	var range = 10;

         	if(maxVal < 10)
         		range = maxVal;

         	var x = 0;
         	var y = image.height + heatmapLegend.marginTop;


         	switch (scaleType)
         	{
         		case 0:
         			colorStep = 100 / (range-1);

         			if(!reverse)
         			{
         				for(var i = 0; i < range; i++)
         				{
         					var color = 'hsl(' + hue + ',' + sat + '%,' + colorStep * i + '%)';
         					matrixCtx.fillStyle = color;

         					x = heatmapLegend.square * index + deltaPadding + heatmapLegend.marginLeft;

         					matrixCtx.fillRect( x, y, heatmapLegend.square, heatmapLegend.square );

         					deltaPadding += heatmapLegend.paddingLeft;
         					index ++;
         				}
         			}
         			else
         			{
         				for(var i = range-1; i >= 0; i--)
         				{
         					var color = 'hsl(' + hue + ',' + sat + '%,' + colorStep * i + '%)';
         					matrixCtx.fillStyle = color;

         					x = heatmapLegend.square * index + deltaPadding + heatmapLegend.marginLeft;

         					matrixCtx.fillRect( x, y, heatmapLegend.square, heatmapLegend.square );

         					deltaPadding += heatmapLegend.paddingLeft;
         					index ++;
         				}
         			}
         			break;

         		case 1:
         			colorStep = HUE_LOW / (range-1);

         			if(!reverse)
         			{
         				for(var i = range-1; i >= 0; i--)
         				{
         					var color = 'hsl(' + colorStep * i + ',' + sat + '%, 50%)';
         					matrixCtx.fillStyle = color;

         					x = heatmapLegend.square * index + deltaPadding + heatmapLegend.marginLeft;

         					matrixCtx.fillRect( x, y, heatmapLegend.square, heatmapLegend.square );

         					deltaPadding += heatmapLegend.paddingLeft;
         					index ++;
         				}
         			}
         			else
         			{
         				for(var i = 0; i < range; i++)
         				{
         					var color = 'hsl(' + colorStep * i + ',' + sat + '%, 50%)';
         					matrixCtx.fillStyle = color;

         					x = heatmapLegend.square * index + deltaPadding + heatmapLegend.marginLeft;

         					matrixCtx.fillRect( x, y, heatmapLegend.square, heatmapLegend.square );

         					deltaPadding += heatmapLegend.paddingLeft;
         					index ++;
         				}
         			}
         			break;
         	}

         	mergedCtx.fillstyle = "#000";
         	mergedCtx.font = "14px Arial";

         	var textPadding = 5;

         	var textPaddingleft = 0;
         	var textPosX_MAX = 0;
         	var textPosY = y + heatmapLegend.square;

         	for(var i = 0; i < range; i++)
         	{
         		textPaddingleft += heatmapLegend.paddingLeft;
         	}

         	textPosX_MAX = range * heatmapLegend.square + textPaddingleft + heatmapLegend.marginLeft + textPadding;

         	mergedCtx.fillText("MIN 1", textPadding, textPosY);						// Set label for minimum value.
         	mergedCtx.fillText('MAX(' + maxVal + ')',textPosX_MAX + 20, textPosY );	// Set label for maximum value.
         }

         /**
          *  Draw the matrix in canvas as heatmap.
          *	@param  {array}	  The matrix data to draw.
          *	@param  {Int}	  The hue value for the heatmap.
          *	@param  {Int}	  The saturation value for the heatmap.
          *	@param  {Int}	  The scale type, 0: grayscale, 1: jet.
          *	@return {Void}.
          */
         function drawMatrixCanvas(data, hue, sat, scaleType, reverse)
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
         				matrixCtx.fillStyle = heatmapColor(data[i][j].val, maxVal, scaleType, hue, sat, reverse);
         				matrixCtx.fillRect( point[0], point[1], 1, 1 );
         			}
         		}
         	}

         	renderHeatmapLegend(scaleType, maxVal, hue, sat, reverse);

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
         	//setImage();
         	matrixCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);
         	mergedCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);

         	if(savedShapes.length > 0 )
         	{
         		var hue = $('#hueLevel').val();
         		var sat = $('#satLevel').val();
         		var scale = 0;
         		var reverse = false;

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

         		if( $('#jetScale[type=checkbox]').is(':checked') )
         			scale = 1;

         		if( $('#reverseScale[type=checkbox]').is(':checked') )
         			reverse = true;



         		var matrix = createMatrix(allMarkedPoints);							// Array with all matrixes and intersect value.
         		drawMatrixCanvas(matrix, hue, sat, scale, reverse);

         		//drawMatrixTable(matrix);

         		var t1 = performance.now();
         		console.log("Render Heatmap took total " + Math.round(t1 - t0) / 1000 + " seconds. \n\n");

         		//return allMarkedPoints;
         	}
         	else
         		alert('No data in database.');

         }






    }; // end of initCanvasMarkingTool()

})(jQuery);
