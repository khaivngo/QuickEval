/**
 *  Creates the heatmap for a specific image.
 *  - Display annotations and heatmap-settings
 */
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
		
		// For heatmap lowest value in Jet scale.
        var HUE_LOW = 240;									
		
		// Browser support:
		var isFirefox = typeof InstallTrigger !== 'undefined';
		
		// Position of heatmap legend:
        var heatmapLegend = new Object();
        heatmapLegend.height = 60;							// Extended value for heatmap legend scale in canvas.
        heatmapLegend.square = heatmapLegend.height / 2;	// Height/width for each square box in the heatmap legend.
        heatmapLegend.marginTop = 5;						// Remember to consider the heatmapLegend.height value when assigning new value.
        heatmapLegend.marginLeft = 75;						// Remember to consider the image.width value when assigning new value.
        heatmapLegend.paddingLeft = 4;						// Padding left for each square box.
		
		// Matrix
		var ImageMatrix = function()
        {
            this.data;
			this.maxVal;
			
			/**
			  * Create a matrix of the experiment image with marked points as data.
			  *	@return {array}	 The matrix.
			  */
            this.createMatrix = function()
            {
				var tempArray = [];
				
                for (var i = 0; i < savedShapes.length; i++)
         		{
         			for(var j = 0; j < savedShapes[i].fill.length; j ++)
         			{
         				tempArray.push([savedShapes[i].fill[j].x, savedShapes[i].fill[j].y] );
         			}
         		}
				
				//var t0 = performance.now();
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

				//var t1 = performance.now();
				//console.log('Init matrix:' + Math.round(t1 - t0) / 1000 + ' seconds.');

				//var t2 = performance.now();

				// Calc matrix: Very good Performance:
				for(var i = 0; i < tempArray.length; i++)
				{
					matrix[ tempArray[i][0] ][ tempArray[i][1] ].val++;
				}

				//var t3 = performance.now();
				//console.log('Calc matrix:' + Math.round(t3 - t2) / 1000 + ' seconds.');

				this.data = matrix;
            }
			
			this.calcMaxValue = function()
			{
				var maxVal = 0;

				// Get max intersection value:
				for(var i = 0; i < this.data.length; i ++)
				{
					for(var j= 0; j < this.data[i].length; j ++)
					{
						if(i == 0)
							maxVal = this.data[i][j].val;
						else
						{
							if(this.data[i][j].val > maxVal)
								maxVal = this.data[i][j].val;
						}
					}
				}
				
				this.maxVal = maxVal;
			}
        };
		
		// Matrix object:
		var imgMatrix = new ImageMatrix();

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
			
			selectTabContent(0);	// Default tab heatmap-settings.
        });
		
		function selectTabContent(tab)
		{
			switch(tab)
			{
				case 0:			// Heatmap settings 
					$('#annotationSection').css('display','none');
					$('#heatmapPanel').css('display','block');
					
				break;
				case 1:			// Annotations
					$('#annotationSection').css('display','block');
					$('#heatmapPanel').css('display','none');
				break;
			}
		}
		
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
                    background: "#ddd"
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
             // fade the canvas and add a loading spinner,
             // removed when artifact marks as been fetched and drawn
             imageCanvas.css({ opacity: 0.7 });
             $('#pan').parent().append('<i style="margin: 20px; position: absolute; top: 0; left: 0;" class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom"></i>');

             $.ajax({
                 url: 'ajax/scientist/getPictureArtifactMarks.php',
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
                        shapes.push({
							fill: JSON.parse(data[i].marked_pixels),
							annotation: data[i].remark,
							visible: true
						});
                    }

                    savedShapes = shapes;
					
					imgMatrix.createMatrix();
					imgMatrix.calcMaxValue();
					
					setScaleType();
                    heatmapMain();
					renderAnnotations();
                }

                // remove loading spinner and set the image opacity back to 100%
                $('.fa-spinner').remove();
                imageCanvas.css({ opacity: 1 });
             });
         }


         function exportHeatmap(experimentID, pictureQueue, pictureID) {

             var settingsObj = {
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
             };

             $.ajax(settingsObj);
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
        };


         $(document).ready(function()
         {
         	// Generate heatmap button.
         	// $('#genHeatmap').on('click', heatmapMain);

			$('.tabSection').on('click', function()
			{
				var index = $('.tabSection').index(this);
				selectTabContent(index);
			});

         	/**
         	 *  UI for heatmap generator.
         	 *	When the user changes the Sliders range, either hue or saturation.
         	 *  @return {void}.
         	 */
         	$('#heatmapPanel li input[type=range]').on('input',function(event)
            {
         		var hue = $('#hueLevel').val();
				var sat = $('#satLevel').val();

				// Change the current label text value for this slider.
				$(this).parent().parent().find('.sizeNumber').val( $(this).val() );

				if(!isFirefox)
					setSliderColor(hue, sat);

				//if( $('#liveGen[type=checkbox]').is(':checked') )
					heatmapMain();
         	});

         	$('#heatmapPanel li input[type=number]').on('input',function(event)
         	{
         		var hue = $('#hueSection input[type=number]').val();
				var sat = $('#satSection input[type=number]').val();
				var opa = $('#opacity-of-marks input[type=number]').val();

				var maxInputVal = 0;

				// Set values as minimum or maximum if exaggerated:
				if( $(this).attr('name') == 'satNumber' )
				{
					maxInputVal = parseInt( $('#satLevel').attr('max') );
				}
				else if( $(this).attr('name') == 'hueNumber' )
				{
					maxInputVal = parseInt( $('#hueLevel').attr('max') );
				}
				else
				{
					maxInputVal = parseInt( $('#opacity-slider').attr('max') );
				}

				if( $(this).val() > maxInputVal )
					$(this).val(maxInputVal);

				if( $(this).val() < 0 )
					$(this).val(0);

				// Set the value for the sliders
				$('#hueLevel').val(hue);
				$('#satLevel').val(sat);
				$('#opacity-slider').val(sat);

				setSliderColor(hue, sat);

				//if( $('#liveGen[type=checkbox]').is(':checked') )
					heatmapMain();
         	});


			 $('#heatmapPanel li input[type=range]').on('change', function(event) {
                heatmapMain();
            });

            $('#reverseScale[type=checkbox]').on('change',function() {
                heatmapMain();
         	});


            // listen for changes to the opacity number input and update the
            // matrix canvas with the new opacity value
            $('#opacity-value').on('input', function() {
         		$('#opacity-slider').val( $(this).val() ); // val()ception

                var opacityLevel = $(this).val() / 100;
         		changeOpacityOfMatrixCanvas(opacityLevel);
         	});

            // listen for changes to the opacity slider and update the
            // matrix canvas with the new opacity value
            $('#opacity-of-marks input[type="range"]').on('input', function() {
                // Change current label text value for this slider.
         		$(this).parent().parent().find('.sizeNumber').val( $(this).val() );

                var opacityLevel = $(this).val() / 100;
         		changeOpacityOfMatrixCanvas(opacityLevel);
         	});

         	document.getElementById('downloadImage').addEventListener('click', function()
         	{
         		downloadCanvas(this, 'heatmapCanvasMerged', 'test.png');
         	}, false);
			
			/*------------------
				Annotations UI
			-------------------*/
			
			$('#annotationList').delegate('li','click', function(event)
			{	
				var target = $(event.target);
				var index = $('#annotationList li').index(this);
				var curActive;
				
				
				// Turn off:
				if( target.is('.visibleStatus[data-visible=1]') )
				{
					target.attr('data-visible','0').html('');
					savedShapes[index].visible = false;
					$(this).attr('class','inactiveAnnotation');
				}
				// Turn off:
				else if (target.is('.visibleStatus[data-visible=1] i') )
				{
					target.parent().attr('data-visible','0').html('');
					savedShapes[index].visible = false;
					$(this).attr('class','inactiveAnnotation');

				}
				// Turn on:
				else if( target.is('.visibleStatus[data-visible=0]') )
				{
					target.attr('data-visible','1').html('<i class="fa fa-eye"></i>');
					savedShapes[index].visible = true;
					
					$('#annotationList li').attr('class','inactiveAnnotation');
					$(this).attr('class','activeAnnotation');
				}
				// Only selection:
				else
				{
					$('#annotationList li').attr('class','inactiveAnnotation');
					$(this).attr('class','activeAnnotation');
				}
				
				// Get index of element that is active:
				$('#annotationList li').each(function()
				{
					if( $(this).hasClass('activeAnnotation') )
						curActive = $('#annotationList li').index(this);
				});
				
				// Render shapes:
				if(curActive != undefined )
					displayAnnotationShapes(curActive);
				else
					displayAnnotationShapes(false);
			});
			
			

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

		function setScaleType()
		{
			var hueRange = document.getElementById("hueLevel");
			var satRange = document.getElementById("satLevel");
			var hueSection = $('#hueSection');
			var satSection = $('#satSection');

			var scaleType = $('#scaleType').find(":selected").index();

			// Reset:
			hueRange.disabled = false;
			hueSection.attr('class','activeSection');

			satRange.disabled = false;
			satSection.attr('class','activeSection');

			// Set preferences for this scale:
			switch(scaleType)
			{
				case 0:									// Jet scale:
					hueRange.disabled = true;
					hueSection.attr('class','inactiveSection');
				break;

				case 1:									// Monochromatic scale:
					/* Magic! */
				break;
			}

			heatmapMain();
		}

        /**
		 *  Change the color of the slider thumb.
		 *	@param  {Int}	The hue level.
		 *	@param  {Int}	The saturation level.
		 *	@return {Void}
		 */
		function setSliderColor(hue, sat)
		{
			for(var i = 0; i < document.styleSheets[1].rules.length; i++)
			{
				var rule = document.styleSheets[1].rules[i];
				if
				(
					rule.cssText.match('::-webkit-slider-thumb') ||
					rule.cssText.match('::-moz-range-thumb') ||
					rule.cssText.match('::-ms-thumb')
				)
				{
					rule.style.backgroundColor = 'hsl('+hue+','+sat+'%,50%)';
				}
			}
		}

         /**
          * @param {float} Number between 0 and 1.
          */
         function changeOpacityOfMatrixCanvas(value) {
             matrixCanvas.css({ opacity: value });
         }

         

         /**
		  * Generate Jet scale color for the heatmap.
		  *	@param  {Float}	  The percentage value for generating the hue level.
		  *	@param  {Int}	  The saturation value.
		  *	@return {String}  color in hsl format.
		  */
         function jetScale(valPerc, sat)
         {
         	var hue = HUE_LOW - (HUE_LOW * valPerc);
         	var color = 'hsl(' + hue + ',' + sat + '%,50%)';
         	return color;
         }
		 /**
		  * Generate grayscale color for the heatmap.
		  *	@param  {Float}	  The percentage value for generating the light level.
		  *	@param  {Int}	  The hue value.
		  *	@param  {Int}	  The saturation value.
		  *	@return {String}  color in grayscale format.
		  */
         function grayScale(valPerc, hue, sat)
         {
         	var light = valPerc * 100;
         	var color = 'hsl(' + hue + ',' + sat + '%,' + light + '%)';
         	return color;
         }

         /**
		  * Generate color for the heatmap.
		  *	@param  {Int}	   The current intersection value for the pixel.
		  *	@param  {Int}	   The highest value of intersections.
		  *	@param	{Int}	   The type of scale that should be rendered in heatmap.
		  *	@param	{Int}	   The hue value.
		  *	@param	{Int}	   The saturation value.
		  *	@param	{Boolean}  Whether the scale should be reversed or not.
		  *	@return {String}   The color in HSL format.
		  */
        function heatmapColor(cur, max, scaleType, hue, sat, reverse)
		{
			var value = (cur-1) / (max-1);		// Float value for generating the color.

			if(reverse) 						// Reverse the scale if true.
				value = 1 - value;

			switch(scaleType) 					// Return the color in HSL format:
			{
				case 0: return jetScale(value, sat);
				case 1: return grayScale(value, hue, sat);

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
          *	@param  {Int}	  The scale type, 0: jet, 1: grayscale.
          *	@return {Void}.
          */
         function drawMatrixCanvas(matrix, hue, sat, scaleType, reverse)
         {
         	//var t0 = performance.now();
         	
         	// Draw matrix with heatmap:
         	for(var i = 0; i < matrix.data.length; i++)
         	{
         		for(var j = 0; j < matrix.data[i].length; j++)
         		{
         			if(matrix.data[i][j].val > 0)
         			{
         				var point = [i,j];
         				matrixCtx.fillStyle = heatmapColor(matrix.data[i][j].val, matrix.maxVal, scaleType, hue, sat, reverse);
         				matrixCtx.fillRect( point[0], point[1], 1, 1 );
         			}
         		}
         	}

         	renderHeatmapLegend(scaleType, matrix.maxVal, hue, sat, reverse);

         	//var t1 = performance.now();
         	//console.log('Render matrix:' + Math.round(t1 - t0) / 1000 + ' seconds.');
         }

		/** 
		 * Render matrix in html table,
		 * (!) not finished.
		 */
		function generateMatrixCSV(matrixData)
		{
			var matrixText = "";

			for(var i = 0; i < 80; i++ )
			{
				for(var j = 0; j < 80; j++ )
				{
					matrixText += matrixData[i][j].val;
				}

				matrixText += "\n";
			}

			$.ajax
			({
				url: 'matrix.php',
				type: 'POST',
				data: {matrix: matrixText}
			})
			.done(function(data)
			{
				//$('body').append('<a download href = "matrix.csv">download file</a>')
			});
		}

         /**
          * Calculate total pixel points for all polygons.
          * Estimates from the current savedShapes.
          * @return {Array} Array of every marked pixel.
          */
         function heatmapMain()
         {
         	//setImage();
         	matrixCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);
         	mergedCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);

         	if(savedShapes.length > 0 )
         	{
         		var hue = $('#hueLevel').val();
         		var sat = $('#satLevel').val();
				var scaleType = $('#scaleType').find(":selected").index();
         		var reverse = false;									// Reverse the color scale.

         		//var t0 = performance.now();

         		//console.log('Before removing dupes: ' + allMarkedPoints.length);
         		//allMarkedPoints = removeDupeVerts(allMarkedPoints);					// Remove duplicated vertices.
         		//console.log('After removing dupes: ' + allMarkedPoints.length);

         		if( $('#reverseScale[type=checkbox]').is(':checked') )
         			reverse = true;

         		drawMatrixCanvas(imgMatrix, hue, sat, scaleType, reverse);

         		//generateMatrixCSV(matrix);

         		//var t1 = performance.now();
         		//console.log("Render Heatmap took total " + Math.round(t1 - t0) / 1000 + " seconds. \n\n");

         		//return allMarkedPoints;
         	}
         	else
         		alert('No data in database.');

         }

		/*------------------------
			Annotations Functions
		  ------------------------*/
		
		function renderAnnotations()
		{
			var annotationList = $('#annotationList');
			var htmlRender = "";
			
			annotationList.empty();
			
			for(var i = 0; i < savedShapes.length; i++)
			{
				if(savedShapes[i].annotation != "")
					htmlRender += '<li class = "inactiveAnnotation">'+
									'<div class = "annotationColumn">' +
										'<div class="visibleStatus" data-visible="1"><i class="fa fa-eye"></i></div>' +
									'</div>'+	
									'<div class = "annotationColumn">' +
										'<div class="shapeIndex">Shape ' + (i+1) +'</div> '+
										'<div class="annotationText"><i>"'+ savedShapes[i].annotation + '"</i></div>' +
									'</div>'+	
								  '</li>';
			}
			annotationList.append(htmlRender);
		}
		
		function displayAnnotationShapes(index)
		{	
			matrixCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);

			for(var i = 0; i < savedShapes.length; i ++)
			{
				for(var j = 0; j < savedShapes[i].fill.length; j++)
				{
					if( savedShapes[i].visible )			
					{
						var point = [ savedShapes[i].fill[j].x, savedShapes[i].fill[j].y ];
						
						// Set selected annotation shape to blue:	
						if(i === index)
							matrixCtx.fillStyle = 'hsla(190,75%,50%,.75)';
						else
							matrixCtx.fillStyle = 'hsla(0,0%,0%,.5)';
						
						matrixCtx.fillRect( point[0], point[1], 1, 1 );
					}
				}
			}
		}




    }; // end of initCanvasMarkingTool()

})(jQuery);
