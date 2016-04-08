<!DOCTYPE html>
<?php
require_once 'functions.php';   //also starts session

//Checks if user is logged in, and the user is scientist or higher level
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['userType'] > 2) {
        header('Location: index.php');
        exit;
    }
} else {
    redirectAfterLogin('scientistpanel.php');
}

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />

        <!-- CSS -->
        <link href="css/metro-bootstrap.css" rel="stylesheet">
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/highslide.css" />
        <link href="css/style.css" rel="stylesheet">

        <!-- JQuery -->
        <script src="js/jquery/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- HighCharts -->
        <script src="highcharts/js/highcharts.js"></script>
        <script src="highcharts/js/highcharts-more.js"></script>
        <script src="highcharts/js/modules/exporting.js"></script>

        <style type="text/css">
            ${demo.css}
        </style>

        <!-- Metro UI -->
        <script src="min/metro.min.js"></script>

        <!-- PLUpload -->
        <script src="plupload/js/plupload.full.min.js"></script>
        <script src="plupload/js/jquery.ui.plupload/jquery.ui.plupload.js"></script>

        <!-- Other JS -->
        <script src="js/scientist/scientistScripts.js"></script>
	    <script src="js/scientist/exportExperiment.js"></script>

        <script src="js/scientist/scientistPanelScripts.js"></script>
        <script src="js/scientist/pictureAlgorithm.js"></script>
        <script src="js/scientist/setUpExperiment.js"></script>
        <script src="js/scientist/manageImageSets.js"></script>
        <script src="js/scientist/fileUploadScripts.js"></script>
        <script src="js/scientist/viewExperiments.js"></script>
        <script src="js/scientist/experimentResults.js"></script>
        <script src="js/scripts.js"></script>
        <script src="js/scientist/highslide-with-gallery.js"></script>

		<script src="js/scientist/math.js" ></script>

        <script type="text/javascript">
            hs.graphicsDir = 'images/graphics/';
            hs.align = 'center';
            hs.transitions = ['expand', 'crossfade'];
            hs.fadeInOut = true;
            hs.outlineType = 'glossy-dark';
            hs.wrapperClassName = 'dark';
            hs.captionEval = 'this.a.title';
            hs.numberPosition = 'caption';
            hs.useBox = true;
            hs.width = 600;
            hs.height = 400;
            //hs.dimmingOpacity = 0.8;

            // Add the slideshow providing the controlbar and the thumbstrip
            hs.addSlideshow({
                //slideshowGroup: 'group1',
                interval: 5000,
                repeat: false,
                useControls: true,
                fixedControls: 'fit',
                overlayOptions: {
                    position: 'bottom center',
                    opacity: 0.75,
                    hideOnMouseOut: true
                },
                thumbstrip: {
                    position: 'above',
                    mode: 'horizontal',
                    relativeTo: 'expander'
                }
            });

            // Make all images animate to the one visible thumbnail
            var miniGalleryOptions1 = {
                thumbnailId: 'thumb1'
            }
        </script>
    </head>
    <body class="metro" style="overflow-y:scroll">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>

            <div id="panels" style="width: 1200px; margin:auto; margin-bottom: 560px;">
                <div id="left-panel" class="span4" style="float:left">
                    <nav class="sidebar light" >
                        <ul>
                            <li class="title">Scientist Panel</li>
                            <li id="dashboard" class="active stick bg-green"><a  href="#"><i class="icon-home"></i>Dashboard</a></li>

                            <li class="title">Images</li>
                            <li id="upload-image"><a href="#"><i class="icon-upload"></i>Upload Image</a></li>
                            <li id="image-sets" ><a href="#"><i class="icon-image"></i>Manage Image Sets</a></li>

                            <li class="title">Experiments</li>
                            <li id="set-up-experiment" ><a href="#"><i class="icon-new"></i>Set Up Experiment</a></li>
                            <li id="view-experiments" ><a href="#"><i class="icon-folder"></i>View Experiments</a></li>
                            <li id="import-experiment" ><a href="#"><i class="icon-upload"></i>Import Experiment</a></li>


                            <li class="title">Other</li>
                            <li id="invite-scientist" ><a href="#"><i class="icon-user"></i>Invite Scientist</a></li>
                            <li id="delete-instruction" ><a href="#"><i class="icon-paragraph-justify"></i>Delete Instructions</a></li>

                        </ul>
                    </nav>
                </div>
                <div id="right-panel" class="span7" style="float: left; margin:20px">
                </div>
                <div id="right-menu" class="bg-steel" style="float: left;">


                </div>
                <div id='below-right-menu' style='float:left'>

                </div>
            </div>
            <?php include_once("includes/footer.html"); ?>
        </div>
    </body>
</html>
