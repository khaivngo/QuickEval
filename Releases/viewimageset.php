<!DOCTYPE html>
<?php session_start(); ?>
<html lang="en">
    <head>
        <meta charset="utf-8">

        <!-- CSS -->
        <link href="css/metro-bootstrap.css" rel="stylesheet">
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">

        <!-- JQuery -->
        <script src="js/jquery/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- Metro UI -->
        <script src="min/metro.min.js"></script>

        <!-- Other JS -->
        <script src="js/scientist/scientistScripts.js"></script>
        <script src="js/scientist/setUpExperiment.js"></script>  
        <script src="js/scientist/manageImageSets.js"></script> 
    </head>
    <body class="metro">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>

            <div id="panels" style="width:1200px; margin:auto; margin-bottom: 560px;">
                <div id="left-panel" class="span4" style="float:left">
                    <nav class="sidebar light" >
                        <ul>
                            <li class="title">Scientist Panel</li>
                            <li id="dashboard" class="active stick bg-green"><a  href="#"><i class="icon-home"></i>Dashboard</a></li>

                            <li class="title">Images</li>
                            <li id="upload-image"><a href="#"><i class="icon-upload"></i>Upload Image</a></li>
                            <li id="view-images" ><a href="#"><i class="icon-pictures"></i>View Images</a></li>
                            <li id="image-sets" ><a href="#"><i class="icon-image"></i>Manage Image Sets</a></li>

                            <li class="title">Experiments</li>
                            <li id="set-up-experiment" ><a href="#"><i class="icon-new"></i>Set Up Experiment</a></li>
                            <li id="view-experiments" ><a href="#"><i class="icon-folder"></i>View Experiments</a></li>
                            <li id="results" ><a href="#"><i class="icon-database"></i>Results</a></li>

                            <li class="title">Other</li>
                            <li id="invite-scientist" ><a href="#"><i class="icon-user"></i>Invite Scientist</a></li>

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