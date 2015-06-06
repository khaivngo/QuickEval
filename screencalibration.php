<!DOCTYPE html>
<html lang="en" style="height: 100%">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="0" />
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
        <script src="js/screenCalibration.js"></script>
        <script src="js/scripts.js"></script>


    </head>


    <?php session_start(); ?>


    <body class="metro" style='background-color: #808080'>
        <div id="wrapper"  >

            <?php include_once("includes/header.html"); ?>
            <div id="alignment" style=" width: 500px; margin: auto">
                <div class="offset0">
                    <br>
                    <h1 class='fg-white'>Brightness Calibration</h1>
                </div>
                <div class="grid">
                    <div class="row">
                        <div class="offset0">
                            <div class="row"style="margin-left: -80px ">
                                <div class="span8"><img src="images/scale.png" alt="Screen calibration scale"> </div>
                            </div>
                            <div class="row" style="margin:auto">
                                <div class="span6">

                                    <p class='fg-white'>    
                                        <br>
                                        <br>
                                        Before you begin the calibration process, 
                                        allow your display the opportunity to warm up" and stabilize. A half-hour to an hour should be sufficient.
                                    </p>

                                    <p class='fg-white'>
                                        Then adjust the brightness so that the darkest colour on each scale is barely visible.
                                    </p>

                                </div>
                                <div id="submit-div" style="margin: 0px 0px 0px 400px" >
                                    <button id="submit" class="button primary span2 large"   />Done</submit>
                                </div>									
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <?php include_once("includes/footer.html"); ?>
        </div>
    </body>


</html>