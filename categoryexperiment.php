<?php
require_once('db.php');
require_once('functions.php');

if (!isset($_SESSION['user']['id'])) {
    if (isset($_GET["invite"])) {
        $hash = $_GET["invite"];
        $url = "categoryexperiment.php?invite=";

        redirectAfterLogin($url . $hash);
    } else {
        header("Location: login.php");
        exit;
    }
}

if (isset($_GET["invite"])) {
    $hash = $_GET["invite"];

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>QuickEval</title>

    <!-- CSS -->
    <link href="css/metro-bootstrap.css" rel="stylesheet">
    <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <link href="css/rating-experiment.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <script src="js/jquery/jquery-3.1.1.min.js"></script>
    <script src="js/jquery/jquery-ui.custom.min.js"></script>

    <!-- jQueryBrowserPlugin-->
    <script src="js/plugins/jquery-browser-plugin-master/dist/jquery.browser.min.js"></script>

    <!-- Metro UI -->
    <script src="min/metro.min.js"></script>

    <!-- Other JS -->
    <script src="js/plugins/jquery.panzoom.min.js"></script>

    <script src="js/commonExperimentScript.js"></script>
    <script src="js/stopwatch.js"></script>
    <script src="js/popup.js"></script>
    <script src="js/Observer/alterExperimentPosition.js"></script>
    <script src="js/ratingExperimentScript.js"></script>
    <script src="js/categoryExperimentScript.js"></script>
</head>
<body
    class="metro"
    style="padding: 0; margin: 0; background-color:#808080;"
    onload="show(); start();"
>

<div id="popupContact">
    <p id="contactArea" class="contactArea-center" style="font-size:18px;">
        Press ESC, Continue or anywhere else to close and continue.
        <br/><br/>
        Click Quit to return to front page.
        <br/><br/>
    </p>

    <div id="popupButtons" class="popupButtons" style="">
        <button id="quit" class="button size2" style="margin-right:10px;">Quit</button>
        <button id="continue" class="button size2">Continue</button>
    </div>
</div>
<div id="backgroundPopup"></div>

<div id="popupContact2">
    <p id="contactArea2" class="contactArea-center" style="font-size:18px;">
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
    <p id="contactArea3" class="contactArea-center" style="font-size:18px;">
        <br/>
        <strong>FINISH</strong>
        <br/><br/>
    </p>

    <div id="popupButtons3" class="popupButtons">
        <button id="quit2" class="button size2" style="margin-right:10px;">Quit</button>
    </div>
</div>
<div id="backgroundPopup3"></div>


<div id="header-div" style="width:100%; height:50px; background-color: #282828;">
    <div class="inner-header">
        <button id="button-instruction" class="button top-buttons" style="margin-left:15px; margin-right:20px">
            Instruction
        </button>
        <button id="enter-fullscreen" class="button fullscreen top-buttons" style="margin-left:10px; "><i
                class="icon-fullscreen"></i>Enter fullscreen
        </button>
        <button id="exit-fullscreen" class="button fullscreenExit top-buttons" style="margin-left:1px; ">Exit
            fullscreen
        </button>

        <span id="ie-info-fullscreen"
              style="color:white;">Press F11 for fullscreen, F11 or ESC to exit fullscreen</span>
        <button class="button cancel-experiment" id="cancel-experiment">Cancel experiment</button>

        <div id="info" class="center" style="color:white;">
            <span id="progress">&nbsp;<span id="time" onload="start();"></span></span>
            <!--Comparison <span id="current-step"></span> of <span id="total-steps"></span> &nbsp |-->
        </div>
    </div>
</div>

<div id="category-container" style="height: 80%; overflow: hidden; padding-top: 50px;">
    <div id="reproduction" class="reproduction panning-container no-original" 
    style="display: inline-block; width: 42%; height: 70%; margin-left: 5%; vertical-align: top;">
        <section id="set2">
            <div class="parent" style="overflow: hidden; position: relative; height: 100%; width: 100%;">
                <a href="" id="reproduction-link" target="_blank" class="new-tab" onclick="return false;"></a>

                <div id="pan2" class="panzoom" style="">
                    <img class="picture" src="images/initiatePicture.png"/>
                </div>
            </div>
        </section>
    </div>


    <div id="original" class="panning-container" style="display:inline-block; vertical-align: top;
    margin-left: 5%; width:42%; height:70%;">
        <span id="original-tag" class="span-original-tag" style="margin-left:50%;"><p>Original</p></span>

        <section id="set1">

            <div class="parent" style="overflow:hidden; position: relative; height:100%; width:100%;">
                <a href="" id="original-link" target="_blank" class="new-tab" onclick="return false;"></a>
                <!--<a href="uploads/6/5/o_18j3b4ooil9k1g0o1k1pcc91vfna.jpeg" id="original-link" target="_blank" class="new-tab"></a >-->
                <div id="pan1" class="panzoom">
                    <img class="picture" src="images/initiatePicture.png"/>
                </div>
            </div>
        </section>

    </div>

    <div class="category-button-container" style="width: 100%; padding: 0; position: absolute; bottom: 5%;">

        <div style="margin-left: 5%; padding-left: 5%; width: 37%; display: inline-block; text-align: center;">
            <div class="input-control select size3">
                <select id="categories" style="background-color:#C8C8C8;">
                    <option value="null" disabled>Select category</option>
                </select>
            </div>
        </div>
        
        <div style="display: inline-block; width: 15%; text-align: center;">
            <button id="panzoom-reset" class="size2">
                <strong>Reset panning</strong>
            </button>
        </div>

        <div style="display: inline-block; width: 36%; text-align: right;">
            <button class="size2" id="button-next-category">
                <strong>Next</strong>
            </button>
        </div>
    
    </div>

</div>


</body>
</html>