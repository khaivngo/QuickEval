<!doctype html>
<html lang="en">

	<?php //require_once("tempdb/DBconnect.php"); ?>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Banana</title>
        <link rel="stylesheet" href="css/master.css" media="screen" title="core styles" charset="utf-8">
        <link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">
    </head>
    <body>

        <div id="main-content-wrapper" class="container">

			<div class="panable" style="width: 400px; height: 600px;">
	            <div class="canvas-container" data-image-url="img/final13.bmp" oncontextmenu="return false;">
	                <!-- image canvas goes here -->
	            </div>
			</div>

			<button id="saveShapeDB" style="position: fixed; top: 0; right: 0;">
				<i class="fa fa-floppy-o"></i>
			</button>

			<div id = "uploadBar"><div id = "bar"></div><div id = "barText"></div></div>


          <!--  <div id="heatmap-panel-container">
    			<ul id = "heatmapPanel">
    				<li>
                        <label>Hue</label><br>
                        <input id = "hueLevel" type="range" value = "0" min="0" max="360">
                        <span class = "sizeNumber">0</span>
                    </li>
    				<li>
                        <label>Saturation</label><br>
                        <input id = "satLevel" type="range" value = "50" min="0" max="100">
                        <span class = "sizeNumber">50</span>
                    </li>
    				<li>
                        <label>Hue scale (Multiple colors)</label><br>
                        <input type = "checkbox" id = "hueScale"><br>
                    </li>
                    <li>
                        <button type = "button" class = "fillAlg">Generate heatmap</button>
                    </li>
    			</ul>
            </div> -->

        </div>


        <script src="libs/jquery.js"></script>
		<script src="libs/jquery.panzoom/dist/jquery.panzoom.js"></script>
        <script src="Helper.js"></script>
        <script src="Annotation.js"></script>
        <script src="canvas-image-marker-testing.js"></script>
        <script>
            $(document).ready(function() {
                $('.canvas-container').canvasMarkingTool({
                    annotation: true
                });

				$(".panable").panzoom();
            });
        </script>

    </body>
</html>
