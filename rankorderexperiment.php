
<?php
require_once('db.php');
require_once('functions.php');

if (!isset($_SESSION['user']['id'])) {
    if (isset($_GET["invite"])) {
        $hash = $_GET["invite"];
        $url = "rankOrderexperiment.php?invite=";

        redirectAfterLogin($url . $hash);
    } else {
        header("Location: login.php");
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
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!-- CSS -->
        <link href="css/metro-bootstrap.css" rel="stylesheet">
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <link href="css/rating-experiment.css" rel="stylesheet">

        <!-- JQuery -->
        <script src="js/jquery/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- Metro UI -->
        <script src="min/metro.min.js"></script>

        <!-- Other JS -->
        <script src="js/plugins/jquery.panzoom.min.js"></script>

        <script src="js/Observer/alterExperimentPosition.js"></script> 
        <script src="js/ratingExperimentScript.js"></script>
        <script src="js/commonExperimentScript.js"></script>
        <script src="js/stopwatch.js"></script>
        <script src="js/popup.js"></script>


        <?php
        //    if (!isset($_SESSION['user'])) {
        //      header("Location: login.php");
        // }
        ?>

    </head>

    <!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->            

    <!--Starts timer-->
    <body class="metro" style="background-color:#808080; overflow:hidden;" onload="show();
            start();">

        <div id="popupContact" style="">
            <p id="contactArea" style="font-size:18px;">
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
            <p id="contactArea2" style="font-size:18px;">
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
                <button id="continue3" class="button size2">Continue</button>
                <button id="quit3" class="button size2" style="margin-right:10px;">Quit</button>
            </div>
        </div>
        <div id="backgroundPopup3"></div>


        <div id="popupContact4">
            <p id="contactArea4"  class="contactArea-center" style="font-size:18px;">
                <br/>
                <strong>NEXT?</strong>
                 <br/><br/>
                You sure you want to go next? All pictures haven't been sorted <br><br> Click Continue to keep sorting or Next to go to next picture set.
            <br/><br/>
        </p>
        <div id="popupButtons4" class="popupButtons">
            <button id="continue4" class="button size2" style="">Coninue</button>
            <button id="button-next-rating" class="button size2">Next</button>
        </div>
    </div>
    <div id="backgroundPopup4"></div>


    <!-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->        

    <div id="header-div">
        <div class="inner-header">
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

    <div id="rating-container">
        <h2 id="original-tag" style="text-align:center; width:100%;">Original</h2>
        <div id="drop-left" class="" style="margin-left: 11%; float:left; height:25%; width: 25%;">
            <section id="set2" style="">
                <div class="parent">
                    <a href="" id="left-reproduction-link" target="_blank" class="new-tab"></a>
                    <div id="pan1" class="panzoom">
                        <img class="picture" src="images/initiatePicture.png"  />
                    </div>
                </div>
                <br>
            </section>
        </div>

        <div id="original" style="margin-left:1%; margin-right:1%; float:left; height:25%; width: 25%;">
            <section id="set2" style="">
                <div class="parent">
                    <a href="" id="original-link" target="_blank" class="new-tab"></a>
                    <div id="pan3" class="panzoom">
                        <img class="picture" src="images/initiatePicture.png"  />
                    </div>
                </div>
                <br>
            </section>
        </div>

        <div id="drop-right" class="" style=" float:left; height:25%; width: 25%;">
            <section id="set2" style="">
                <div class="parent">
                    <a href="" id="right-reproduction-link" target="_blank" class="new-tab"></a>
                    <div id="pan2" class="panzoom">
                        <img class="picture" src="images/initiatePicture.png"  />
                    </div>
                </div>
            </section>
        </div>
    </div>

    <button id="button-finished" class="size2 button-finished" ><strong>Next</strong></button>


    <div id="rating" class="footer rating-collection center" style="">
        <div id="rating-images" ></div>

    </div>

</body>
</html>