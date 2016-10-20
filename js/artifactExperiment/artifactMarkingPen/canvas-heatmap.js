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

		// Browser support:
		var isFirefox = typeof InstallTrigger !== 'undefined';

/*---------------------------------------------------------------------------*/

		// Position of heatmap legend:
        var heatmapLegend = new Object();
        heatmapLegend.height = 60;							// Extended value for heatmap legend scale in canvas.
        heatmapLegend.square = heatmapLegend.height / 2;	// Height/width for each square box in the heatmap legend.
        heatmapLegend.marginTop = 5;						// Remember to consider the heatmapLegend.height value when assigning new value.
        heatmapLegend.marginLeft = 75;						// Remember to consider the image.width value when assigning new value.
        heatmapLegend.paddingLeft = 4;						// Padding left for each square box.

		/**
		 *  Class Shape_matrix.
		 *  Creates a matrix based on the shapes for this particular image.
		 *  Used in heatmap.
		 */
		var Shape_matrix = function()
        {
            this.data;					// The matrix in a 2D array format.
			this.maxVal;				// The matrix element with highest value.
			this.csv;					// The matrix in CSV format (String).

			/**
			 *  Create a matrix of the experiment image with marked points as data.
			 *	Sets value to property this.data.
			 */
            this.createMatrix = function()
            {
				var shapesArray = [];										// Keeps all saved shapes in array format.

                for (var i = 0; i < savedShapes.length; i++)				// Loop all shapes.
         		{
					if(savedShapes[i].eyeVisible && !savedShapes[i].hidden) // The shape is active.
					{
						for(var j = 0; j < savedShapes[i].fill.length; j++) // Loop all coordinates for this shape.
						{
																			// Add shape coordinate to array.
							shapesArray.push
							([
								savedShapes[i].fill[j].x,
								savedShapes[i].fill[j].y
							]);
						}
					}
         		}
				//console.log('Number of markings: ' + shapesArray.length);
				var t0 = performance.now();

				var matrix = [];							// Initialize matrix array.
															// Create matrix dimensions:
				for (var i = 0; i < image.width; i++) 		// Loop all rows:
				{
					matrix[i] = []; 						// Set matrix as a 2D array.
					for (var j = 0; j < image.height; j++) 	// Loop all columns:
					{
						matrix[i][j] = {val: 0}; 			// Set matrix element to default value 0.
					}
				}


				// Set marking values to matrix elements:
				for(var i = 0; i < shapesArray.length; i++)
				{
					matrix[ shapesArray[i][0] ][ shapesArray[i][1] ].val++;
				}

				var t1 = performance.now();
				//console.log('Init matrix:' + Math.round(t1 - t0) / 1000 + ' seconds.');

				//var t3 = performance.now();
				//console.log('Calc matrix:' + Math.round(t3 - t2) / 1000 + ' seconds.');

				this.data = matrix;
            }
			/**
			 *  Calculate the matrix element with the highest value.
			 *	Sets value to property this.maxVal (max value).
			 */
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
			/**
			 *  Convert the matrix to CSV format.
			 *  Sets value to property this.csv.
			 */
			this.generateCSV = function()
			{
				var matrixText = "";

				for(var i = 0; i < image.width; i++ )
				{
					for(var j = 0; j < image.height; j++ )
					{
						matrixText += this.data[i][j].val;

						if(j+1 < image.height)
							matrixText += ',';
					}
					matrixText += "\n";
				}

				this.csv = matrixText;
			}
			/**
			 *  Send CSV to PHP via ajax, where PHP will
			 *  generate a CSV file on the server, ready to download.
			 */
			this.saveCSVtoServer = function()
			{
				var fileName = settings.pictureName.toString() + '_heatmap_matrix';

				$.ajax
				({
					url: 'js/artifactExperiment/artifactMarkingPen/CSV/heatmapMatrixCSV.php',
					type: 'POST',
					data: {matrix: this.csv, fileName: fileName}
				})
				.done(function(data)
				{
					$('#downloadHeatmapFile')
                        .attr('href','js/artifactExperiment/artifactMarkingPen/CSV/' + fileName + '.csv');
				});
			}
        };
		// End class Shape_matrix.

		/**
		 *  Class Heatmap_preferences.
		 *  Sets the heatmap properties for the choosen scale.
		 *  Used in heatmap.
		 */
		var Heatmap_settings = function()
		{
			// Properties:

			this.scaleType = 0; 			// {Int} 	 - Scale type for heatmap.
			this.reverse = false;			// {Boolean} - Whether the scale should be reversed or not.


			// Set functions:

			this.setScaleType = function(scaleType)
			{
				var hueRange = document.getElementById("hueLevel");
				var satRange = document.getElementById("satLevel");
				var hueSection = $('#hueSection');
				var satSection = $('#satSection');

				// Reset:
				hueRange.disabled = false;
				satRange.disabled = false;
				hueSection.attr('class','activeSection');
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

					case 2:									// Gray scale:
						hueRange.disabled = true;
						hueSection.attr('class','inactiveSection');
						satRange.disabled = true;
						satSection.attr('class','inactiveSection');
					break;
				}

				this.scaleType = scaleType;
			}

			this.setReverse = function(status)
			{
				this.reverse = status;
			}
		};
		// End class Heatmap_settings.

		/**
		 *  Class Heatmap_preferences.
		 *  Sets the heatmap properties for the chosen scale.
		 *  Used in heatmap.
		 */
		var Annotation = function()
		{
			// Set functions:

			/**
			 *  Display only shapes with a comment.
			 *  @param {Boolean} Whether the shapes without a comment is visible or not.
			 */
			this.setOnlyComments = function(status)
			{
				// Display only shapes with a comment:
				if(status)
				{
					for(var i = 0; i < savedShapes.length; i ++)
					{
						if(savedShapes[i].annotation != " ") 	// Has comment:
							savedShapes[i].hidden = false;
						else
							savedShapes[i].hidden = true;
					}
				}
				// Reset to display all shapes:
				else
				{
					for(var i = 0; i < savedShapes.length; i ++)
					{
						savedShapes[i].hidden = false;
					}
				}
			}

			this.displayShapes = function(index)
			{
				// Reset matrix canvas:
				matrixCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);

				// Loop all shapes and display shape with a background to a transparent blackish colour:
				for(var i = 0; i < savedShapes.length; i ++)
				{
					for(var j = 0; j < savedShapes[i].fill.length; j++)
					{
						if( savedShapes[i].eyeVisible && !savedShapes[i].hidden )
						{
							var point = [ savedShapes[i].fill[j].x, savedShapes[i].fill[j].y ];

							/*  Draw the shape unless the shape
								is selected, aka index value: 	*/
							if(i !== index)
							{
								matrixCtx.fillStyle = 'hsla(0,0%,0%,.25)';
								matrixCtx.fillRect( point[0], point[1], 1, 1 );
							}
						}
					}
				}

				// Set selected annotation shape to blue:
				if (( index != false || index === 0 ) && savedShapes[index].eyeVisible )
				{
					var x, y = 0;

					for(var i = 0; i < savedShapes[index].fill.length; i++)
					{
						x = savedShapes[index].fill[i].x;
						y = savedShapes[index].fill[i].y;

						matrixCtx.fillStyle = 'hsla(190,75%,50%,.9)';			// Blue color.
						matrixCtx.fillRect( x, y, 1, 1 );
					}
				}
			}

			this.renderAnnotations = function()
			{
				var annotationList = $('#annotationList');
				var htmlRender = "";

				annotationList.empty();

				for(var i = 0; i < savedShapes.length; i++)
				{
					if(!savedShapes[i].hidden)
					{
						var annotation = "";
						if(savedShapes[i].annotation != " ")
							annotation = savedShapes[i].annotation;
						else
							annotation = "No comment";

						htmlRender +=
						'<li class = "inactiveAnnotation" title = "Shape object">'+
							'<div class = "annotationColumn">' +
								'<div class="visibleStatus" data-visible="1" title="Visibility"><i class="fa fa-eye"></i></div>' +
							'</div>'+
							'<div class="annotationColumn">' +
								'<div class="shapeIndex" title = "Shape index">Shape ' + (i+1) +'</div> '+
								'<div class="annotationText" title = "Comment"><i>"'+ annotation + '"</i></div>' +
							'</div>'+
						'</li>';
					}
				}
				annotationList.append(htmlRender);
			}
		};
		// End class Annotation.

/*---------------------------------------------------------------------------*/

		/*--- Global variables: ---*/

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

        var savedShapes;										// Array, stores all shapes objects.

		// Init objects:
		var heatmapSettingsObj 	  = new Heatmap_settings(); 	// Heatmap_preferences object.
		var shapeMatrixObj	  	  = new Shape_matrix();			// Shape_matrix object.
		var annotationObj		  = new Annotation();			// Annotations_preferences object.

        $(document).ready(function()
		{
															// This is so hacky..
			$('#annotationList').undelegate('click');		// Reset delegate click event.
			$('#annotationToolbar li').unbind();			// Reset click event.
			$('#scaleType').unbind();						// Reset click event.
			$('#reverseScale').unbind();					// Reset click event.
			$('.advancedOptionsButton').unbind();			// Reset click event.

            setCanvasImage();
            setImage();

            $(canvasContainer).append(imageCanvas); 		// Append the resized canvas to the DOM
            $(canvasContainer).append(matrixCanvas); 		// Append the resized canvas to the DOM
            $(canvasContainer).append(mergedCanvas); 		// Append the resized canvas to the DOM


            // settings.pictureID
            getArtifactMarks(settings.experimentID, settings.pictureQueue);

			selectTabContent(0);							// Default tab heatmap-settings.


         	// $('#genHeatmap').on('click', heatmapMain);	// Generate heatmap button.

			$('.tabSection').on('click', function()
			{
				var index = $('.tabSection').index(this);
				selectTabContent(index);
			});



			/**
			 *  Toolbar for the heatmap settings
			 *
			 */

			$('#scaleType').on('change',function()
			{
				var scaleType = $('#scaleType').find(":selected").index();
				heatmapSettingsObj.setScaleType(scaleType);
				heatmapMain();
			});

			$('#reverseScale').on('click',function()
			{
				//var status = setStatusToolbarButton( $(this) );

				var status = ( $(this).is(':checked') ) ? true : false;

				heatmapSettingsObj.setReverse(status);
				heatmapMain();
			});

			// Toggle heatmap scale colour sliders:
			$('.advancedOptionsButton').on('click',function()
			{
				var section = $(this).attr('data-target');
				var textStatus = "";
				if( $('#' + section).is(':hidden') )
				{
					$('#' + section).slideDown();
					textStatus = "Hide";
				}
				else
				{
					$('#' + section).slideUp();
					textStatus = "Show";
				}

				$(this).find('span').text(textStatus);

				setHeatmapSettingsHeight();
			});

         	/**
         	 *  UI for heatmap colour generator.
         	 *	When the user changes the Sliders range, either hue, saturation or opacity.
         	 *  @return {void}.
         	 */
         	$('#scaleColorSettings li input[type=range]').on('input',function(event)
            {
         		var hue = $('#hueLevel').val();
				var sat = $('#satLevel').val();

				// Change the current label text value for this slider.
				$(this).parent().parent().find('.sizeNumber').val( $(this).val() );

				if(!isFirefox)
					setSliderColor(hue, sat);
         	});

			$('#scaleColorSettings li input[type=range]').on('change', function(event) {
                heatmapMain();
            });

         	$('#scaleColorSettings li input[type=number]').on('input',function(event)
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

			$('#exportHeatmapFileSelect').on('change',function()
			{
				var selectedFile = $(this).find(":selected").attr('name');	// Get selected file.
				var downloadLink = $('#downloadHeatmapFile');

                console.log(selectedFile);
                console.log(downloadLink);

				downloadLink.attr('href','').attr('download','');			// Reset download link button.

				switch( selectedFile ) 										// Assign download link, based on the selected file.
				{
					case "matrixCSV":
						shapeMatrixObj.saveCSVtoServer();
					break;

					case "imagePNG":
						var fileName = settings.pictureName.toString() + '_heatmap_image';
						var link = document.getElementById('downloadHeatmapFile');

						downloadCanvas(link, 'heatmapCanvasMerged', fileName + '.png');

					break;

					case "gibberish":
						/* Magic! */
					break;
				}

				downloadLink.find('button').prop("disabled", false);

			});

			/*------------------
				Annotations UI
			-------------------*/

			/**
			 *  Toolbar for the annotation settings
			 *
			 */
			$('#hideAllAnnotations').on('click',function()
			{
				var status = setStatusToolbarButton( $(this) );

				for(var i = 0; i < savedShapes.length; i ++)
				{
					savedShapes[i].eyeVisible = false;
				}

				$('.visibleStatus').attr('data-visible','0').html('');
				$('#annotationList li').attr('class','inactiveAnnotation');
				annotationObj.displayShapes(false);
			});

			$('#showAllAnotations').on('click',function()
			{
				var status = setStatusToolbarButton( $(this) );

				for(var i = 0; i < savedShapes.length; i ++)
				{
					savedShapes[i].eyeVisible = true;
				}

				$('.visibleStatus').attr('data-visible','1').html('<i class="fa fa-eye"></i>');
				$('#annotationList li').attr('class','inactiveAnnotation');
				annotationObj.displayShapes(false);
			});

			$('#onlyAnnotations').on('click',function()
			{
				var status = setStatusToolbarButton( $(this) );

				annotationObj.setOnlyComments(status);
				annotationObj.renderAnnotations();
				annotationObj.displayShapes(false);

			});

			// End annotation toolbar.

			/**
			 *  Select an annotation from the list.
			 *  Shape will be highlighted in the canvas.
			 *  Possible to show/hide a shape by toggling the eye icon.
			 */
			$('#annotationList').delegate('li','click', function(event)
			{
				var target = $(event.target);
				var index = $('#annotationList li').index(this);
				var curActive;

				// Turn off:
				if( target.is('.visibleStatus[data-visible=1]') )
				{
					target.attr('data-visible','0').html('');
					savedShapes[index].eyeVisible = false;
					$(this).attr('class','');					// Reset all items.
					$('#annotationToolbar li[data-section=0]').removeClass('activeTool');
				}
				// Turn off:
				else if (target.is('.visibleStatus[data-visible=1] i') )
				{
					target.parent().attr('data-visible','0').html('');
					savedShapes[index].eyeVisible = false;
					$(this).attr('class','');					// Reset all items.
					$('#annotationToolbar li[data-section=0]').removeClass('activeTool');
				}
				// Turn on:
				else if( target.is('.visibleStatus[data-visible=0]') )
				{
					target.attr('data-visible','1').html('<i class="fa fa-eye"></i>');
					savedShapes[index].eyeVisible = true;

					$('#annotationList li').attr('class','');	// Reset all items.
					$(this).attr('class','activeAnnotation');

					$('#annotationToolbar li[data-section=0]').removeClass('activeTool');
				}
				// Only selection:
				else
				{
					$('#annotationList li').attr('class','');	// Reset all items.
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
					annotationObj.displayShapes(curActive);
				else
					annotationObj.displayShapes(false);
			});


			/*---
				Window listeners
								---*/
			$(window).resize(function()
			{
				setHeatmapSettingsHeight();
			});

        }); // End doc ready.


		/* Window Functions */

		function setHeatmapSettingsHeight()
		{
			var headerHeight = parseInt( $('.heatmapPanelHeader').outerHeight(true) + $('.tabSection').outerHeight(true) );

			// console.log($(window).height() - headerHeight);

			$('#heatmapSection').css('height', $(window).height() - headerHeight );
		}

		/* End window functions */

		function selectTabContent(tab)
		{
			// Reset:
			$('.tabSection').removeClass('activeTab');

			switch(tab)
			{
				case 0:			// Heatmap settings
					$('#heatmapTab').addClass('activeTab');

					$('#annotationSection').css('display','none');
					$('#heatmapSection').css('display','block');
				break;

				case 1:			// Annotations
					$('#annotationTab').addClass('activeTab');

					$('#annotationSection').css('display','block');
					$('#heatmapSection').css('display','none');
				break;
			}
		}

		/**
		 * Set status for toolbar button
		 */
		function setStatusToolbarButton(elem)
		{
			var buttonStatus = true;
			var parent = elem.parent();

			if( $(elem).hasClass('activeTool') )
			{
				$(elem).removeClass('activeTool');
				buttonStatus = false;
			}

			if( $(elem).attr('data-section') && buttonStatus )
			{
				// Reset active class for this section.
				var section = parseInt( $(elem).attr('data-section') );	// Int value for tool section.
				$( parent.find('li[data-section=' + section + ']')).removeClass('activeTool');
			}

			if(buttonStatus)
				$(elem).addClass('activeTool');

			return buttonStatus;
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
                     // picture_id: pictureID,
                     picture_queue: pictureQueue
                 },
                 dataType: 'json',
                 encode: true,
                 cache: false
             })
             .done(function(data) {
                // if (data.length > 0) {
                    var shapes = [];
                    for (var i = 0; i < data.length; i++) {
                        shapes.push({
							fill: JSON.parse(data[i].marked_pixels),
							annotation: data[i].remark,
							eyeVisible: true,
							hidden: false
						});
                    }

					savedShapes = shapes;

					shapeMatrixObj.createMatrix();
					shapeMatrixObj.calcMaxValue();
					shapeMatrixObj.generateCSV();

					heatmapSettingsObj.setScaleType(0);

					annotationObj.renderAnnotations();

					setHeatmapSettingsHeight();

                    heatmapMain();
                // }

                // remove loading spinner and set the image opacity back to 100%
                $('.fa-spinner').remove();
                imageCanvas.css({ opacity: 1 });
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
        };

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

        /**
		 *  Change the color of the slider thumb.
		 *	@param  {Int}	The hue level.
		 *	@param  {Int}	The saturation level.
		 *	@return {Void}
		 */
		function setSliderColor(hue, sat)
		{
			for(var i = 0; i < document.styleSheets[4].rules.length; i++)
			{
				var rule = document.styleSheets[4].rules[i];
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
		 * Generate Jet scale colour for the heatmap.
		 *	@param  {Float}	  The percentage value for generating the hue level.
		 *	@param  {Int}	  The saturation value.
		 *	@return {String}  colour in HSL format.
		 */
        function jetScale(valPerc, sat)
        {
			var hue = 240 - (240 * valPerc);
			var color = 'hsl(' + hue + ',' + sat + '%,50%)';
			return color;
        }

		 /**
		  * Generate monochromatic colour for the heatmap.
		  *	@param  {Float}	  The percentage value for generating the light level.
		  *	@param  {Int}	  The hue value.
		  *	@param  {Int}	  The saturation value.
		  *	@return {String}  colour in monochromatic format.
		  */
         function monochromaticScale(valPerc, hue, sat)
         {
         	var light = valPerc * 100;
         	var color = 'hsl(' + hue + ',' + sat + '%,' + light + '%)';
         	return color;
         }

        /**
		 * Generate colour for the heatmap.
		 *	@param  {Int}	   The current matrix element value.
		 *	@param  {Int}	   The highest matrix element value.
		 *	@param	{Int}	   The type of scale that should be rendered in heatmap.
		 *	@param	{Int}	   The hue value.
		 *	@param	{Int}	   The saturation value.
		 *	@param	{Boolean}  Whether the scale should be reversed or not.
		 *	@return {String}   The colour in HSL format.
		 */
        function heatmapColor(cur, max, scaleType, hue, sat, reverse)
		{
			if(max-1 == 0) 						// Avoid NaN when max-1 equals 0.
				max = 2;

			var value = (cur-1) / (max-1);		// Float value for generating the colour.

			if(reverse) 						// Reverse the scale if true.
				value = 1 - value;

			switch(scaleType) 					// Return the colour in HSL format:
			{
				case 0: return jetScale(value, sat);
				case 1: return monochromaticScale(value, hue, sat);
				case 2: return monochromaticScale(value, hue, sat);		// Grayscale.

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
         			colorStep = 240 / (range-1);

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

         		case 1:
				case 2:
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

         	mergedCtx.fillText("MIN 1", textPadding, textPosY);					// Set label for minimum value.
         	mergedCtx.fillText('MAX ' + maxVal, textPosX_MAX + 20, textPosY );	// Set label for maximum value.
         }

         /**
          * Draw the heatmap in canvas.
          *	@param  {Object}   The matrix data to draw.
          *	@param  {Int}	   The hue value for the heatmap.
          *	@param  {Int}	   The saturation value for the heatmap.
          *	@param  {Int}	   The scale type, 0: jet, 1: grayscale.
          *	@param	{Boolean}  Whether the scale should be reversed or not.
          *	@return {Void}.
          */
         function drawHeatmap(matrix, hue, sat, scaleType, reverse)
         {
         	// Loop the matrix rows and columns:
         	for(var i = 0; i < matrix.data.length; i++)
         	{
         		for(var j = 0; j < matrix.data[i].length; j++)
         		{
					// The point is marked, draw this point:
         			if(matrix.data[i][j].val > 0)
         			{
						// Set background colour:
         				matrixCtx.fillStyle = heatmapColor(matrix.data[i][j].val, matrix.maxVal, scaleType, hue, sat, reverse);

						// Draw point in canvas, as 1 by 1 px:
						matrixCtx.fillRect( i, j, 1, 1 );
         			}
         		}
         	}

         	renderHeatmapLegend(scaleType, matrix.maxVal, hue, sat, reverse);
         }

         /**
          * Calculate total pixel points for all polygons.
          * Estimates from the current savedShapes.
          * @return {Void}.
          */
         function heatmapMain()
         {
         	//setImage();
         	matrixCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);
         	mergedCtx.clearRect(0, 0, image.width, image.height + heatmapLegend.height);

     		var hue = $('#hueLevel').val();
     		var sat = $('#satLevel').val();
			var scaleType = heatmapSettingsObj.scaleType;
     		var reverse   = heatmapSettingsObj.reverse;

			if(scaleType == 0) 	// Jet scale has no hue. set defualt 0.
				hue = 0;

			else if(scaleType == 2) // Grayscale.
			{
				hue = 0;
				sat = 0;
			}

     		drawHeatmap(shapeMatrixObj, hue, sat, scaleType, reverse);
         }

		/*---------------------------
			Annotations Functions
		 --------------------------*/


		function manualSort(arr, fromIndex, toIndex)
		{
			var element = arr[fromIndex];
			arr.splice(fromIndex, 1);
			arr.splice(toIndex, 0, element);
			return arr;
		}

		function sortByObject()
		{
			/* Magic! */
		}

		//function filter








    }; // end of initCanvasMarkingTool()

})(jQuery);
