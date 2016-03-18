<!doctype html>
<html lang="en">

	<?php require_once("tempdb/DBconnect.php"); ?>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Banana</title>
        <link rel="stylesheet" href="master.css" media="screen" title="core styles" charset="utf-8">
        <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    </head>
	
    <body>
        <div id="main-content-wrapper" class="container">

            <div id="marking-tool-panel">
                <button id="undo"><i class="fa fa-undo"></i></button>
                <button id="marking-tool">
                    <i class="fa fa-pencil-square-o"></i>
                    <i class="fa fa-eraser"></i>
                </button>
				
				<button id="saveShapeDB"><i class="fa fa-floppy-o"></i><span style = "font-size: 50%;"> Save to db, klikk på denne etter endt brukertest<br> (OBS, klikk bare en gang for å unngå dupes( Refresh page etter brukertest(peneste knappen jeg har laget noen gang))).</span></button>
            </div>

          <!--  <div class="canvas-container" data-image-url="flora.jpg" data-id="1" oncontextmenu="return false;">
                <!-- image canvas goes here -->
                <!-- heatmap canvas goes here -->
          <!--  </div> -->

            <div class="canvas-container" data-image-url="free1.jpg" data-id="2"  oncontextmenu="return false;">
                <!-- image canvas goes here -->
                <!-- heatmap canvas goes here -->
            </div>

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


        <script src="jquery.js"></script>
        <script src="Helper.js"></script>
        <script src="Annotation.js"></script>
        <script src="canvas-image-marker.js"></script>
        <script>
            $(document).ready(function() {
                $('.canvas-container').canvasMarkingTool({
                    annotation: true
                });
            });
        </script>

    </body>
</html>
