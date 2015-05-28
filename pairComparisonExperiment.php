<?php
require_once('db.php');
require_once('functions.php');

if (!isset($_SESSION['user']['id'])) {
    if (isset($_GET["invite"])) {
        $hash = $_GET["invite"];
        $url = "pairComparisonExperiment.php?invite=";
        
        redirectAfterLogin($url . $hash);
    }else   {
        header("Location: login.php");
    }
    
}
if (isset($_GET["invite"])) {
    $hash = $_GET["invite"];
    $url = "pairComparisonExperiment.php?invite=";


    try {
        $stmt = $db->query("SELECT id FROM experiment WHERE inviteHash = '" . $hash . "'");

        $res = $stmt->fetchAll();
        $_SESSION['experimentId'] = $res[0]['id'];
    } catch (Exception $ex) {
        
    }
} else {
    $_SESSION['experimentId'] = $_POST['experimentId'];
}
?>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <!-- CSS -->
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <link href="css/metro-bootstrap.css" rel="stylesheet">

        <link href="css/rating-experiment.css" rel="stylesheet">

        <!-- JQuery -->
        <script src="js/jquery/test/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- Other JS & plugins -->
        <script src="js/plugins/jquery.panzoom.min.js"></script>

        <script src="js/popup.js"></script>
        <script src="js/commonExperimentScript.js"></script>
        <script src="js/scientist/scientistScripts.js"></script> 
        <script src="js/Observer/alterExperimentPosition.js"></script>
        <script src="js/experimentScripts.js"></script> 
        <script src="js/stopwatch.js"></script>
    </head>

    <body class="metro" style="background-color:#808080;" onload="show();
            start();">

        <div id="popupContact" style="">
            <p id="contactArea"  class="contactArea-center"  style="font-size:18px;">
                Press ESC, Continue or anywhere else to close and continue.
                <br/><br/>
                Click Quit to return to front page.
                <br/><br/>
            </p>
            <div id="popupButtons" class="popupButtons" style="">
                <button id="quit" class="button size2" >Quit</button>
                <button id="continue" class="button size2">Continue</button>
            </div>
        </div>
        <div id="backgroundPopup"></div>

        <div id="popupContact2">
            <p id="contactArea2"  class="contactArea-center"  style="font-size:18px;">
                <br/>
                <strong>INSTRUCTION</strong>
                <br/><br/>
            </p>
            <div id="popupButtons2" class="popupButtons">
                <button id="continue2" class="button">Continue</button>
            </div>
        </div>
        <div id="backgroundPopup2"></div>

        <div id="popupContact3">
            <p id="contactArea3"  class="contactArea-center" style="font-size:18px;">
                <br/>
                <strong>FINISH</strong>
                <br/><br/>
            </p>
            <div id="popupButtons3" class="popupButtons">
                <button id="quit2" class="button size2" style="margin-right:10px;">Quit</button>
            </div>
        </div>
        <div id="backgroundPopup3"></div>

        <!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->        


        <div id="paircomparison">

            <div id="header-div" style="width:100%; height:50px; background-color: #282828;">
                <div style="margin-top:5px;">
                    <button id="button-instruction" class="button top-buttons" style="margin-left:15px; margin-right:20px">Instruction</button>
                    <button id="enter-fullscreen" class="button fullscreen top-buttons" style="margin-left:10px; "><i class="icon-fullscreen"></i>Enter fullscreen</button>
                    <button id="exit-fullscreen" class="button fullscreenExit top-buttons" style="margin-left:1px; ">Exit fullscreen</button>

                    <span id="ie-info-fullscreen" style="color:white;">Press F11 for fullscreen, F11 or ESC to exit fullscreen</span>
                    <button class="button cancel-experiment" id="cancel-experiment" >Cancel experiment</button>

                    <div id="info" class="center" style="color:white;">
                        <span id="progress">&nbsp<span id="time" onload="start();"></span></span> 
                    </div>
                </div> 
            </div>

            <div id="pair-container">
                <span id="original-tag" style="text-align:center; width:100%;"><p>Original</p></span>
                <div id="left-reproduction" class="" style=" display:inline-block; margin-left: 0; width:30%; height: 60%;">
                    <section id="set2" style="">
                        <div class="parent" style="overflow:hidden; position: relative; height: 100%; width: 100%;">
                            <a href="" id="left-reproduction-link" target="_blank"  class="new-tab" onclick="return false;"></a >
                            <div id="pan1" class="panzoom">
                                <img id="pictureLeft" class="picture" src="flower.jpeg"  />
                            </div>
                        </div>
                    </section>
                </div>

                <div id="original" class="" style="margin-left: 2%; width:30%; height:60%; display:inline-block; ">
                    <section id="set2" style="">
                        <div class="parent" style="overflow:hidden; position: relative; height: 100%; width: 100%;">
                            <a href="" id="original-link" target="_blank" class="new-tab" onclick="return false;"></a >
                            <div id="pan3" class="panzoom">
                                <img id="pictureOriginal" class="picture" src="images/initiatePicture.png"  />
                            </div>
                        </div>
                    </section>
                </div>

                <div id="right-reproduction" class="right pictureContainer" style="display:inline-block; margin-left: 2%; width:30%; height: 60%">
                    <section id="set2" style="">
                        <div class="parent" style="overflow:hidden; position: relative; height: 100%; width: 100%;">
                            <a href="" id="right-reproduction-link" target="_blank" class="new-tab" onclick="return false;"></a >
                            <div id="pan2" class="panzoom">
                                <img id="pictureRight" class="picture" src="images/initiatePicture.png"  />
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div id="bottom-buttons" class="page-bottom" style="position:absolute;">
                <button class="button" id="button-none" style="width: 100%; height:25px; margin-bottom: 10px;">None</button>
                <button class="button" id="button-next" style="width: 100%; height:25px;  ">Next</button>
            </div>
        </div>

    </body>
</html>