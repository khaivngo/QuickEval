<!DOCTYPE html>
<?php require_once 'functions.php';   //also starts session ?>
<?php
    if (!isset($_SESSION['user']['id'])) {
        redirectAfterLogin('index.php');
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
        <link href="css/style.css" rel="stylesheet">
        <link href="css/jquery.confirmon.css" rel="stylesheet">

        <!-- JQuery -->
        <script src="js/jquery/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- Metro UI -->
        <script src="min/metro.min.js"></script>

        <!-- Other JS -->
        <script src="js/plugins/jquery.confirmon.js"></script>

        <script src="js/scientist/exportExperiment.js"></script>
        <script src="js/indexScripts.js"></script>
        <script src="js/scripts.js"></script>
        <script src="js/Observer/alterExperimentPosition.js"></script>

        <?php echo (isset($_GET['invite'])) ? '<input type="hidden" id="invite" value="'. $_GET['invite'] .'"/>'  : '' ?>

    </head>
    <body class="metro" style="overflow-y:scroll">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>
            <div id="panels" style="width: 1000px; margin: auto;">
                <div id="top-panels" style="max-height: 500px; width: 100%">
                    <h1>Select Experiment</h1>
                    <div class="accordion" data-role="accordion" style="width:49%; float:left; display: inline;">
                        <div id="method" class="accordion-frame">
                            <a href="#" class="active heading">Method</a>
                            <div class="content">
                                <div class="listview-outlook" data-role="listview" style="max-height: 250px; overflow:auto">
                                    <div id="method-list" class="list-group ">
                                        <div id="select-method" class="group-content">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="institute" class="accordion-frame">
                            <a href="#" class="heading">Institution</a>
                            <div class="content">
                                <div class="input-control text" data-role="input-control">
                                    <input id="institution-search" type="text">
                                    <button class="btn-search"></button>
                                </div>
                                <div class="listview-outlook" data-role="listview" style="max-height: 250px; overflow:auto">
                                    <div id="institute-list" class="list-group ">
                                        <div id="select-institution" class="group-content">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="organization" class="accordion-frame">
                            <a href="#" class="heading">Organization</a>
                            <div class="content">
                                <div class="input-control text" data-role="input-control">
                                    <input id="organization-search" type="text">
                                    <button class="btn-search"></button>
                                </div>
                                <div class="listview-outlook" data-role="listview" style="max-height: 250px; overflow:auto">
                                    <div id="organization-list" class="list-group ">
                                        <div id="select-organization"class="group-content">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="scientist" class="accordion-frame">
                            <a href="#" class="heading">Scientist</a>
                            <div class="content">
                                <div class="input-control text" data-role="input-control">
                                    <input id="scientist-search" type="text">
                                    <button class="btn-search"></button>
                                </div>
                                <div class="listview-outlook" data-role="listview" style="max-height: 250px; overflow:auto">
                                    <div id="scientist-list" class="list-group ">
                                        <div id="select-scientist" class="group-content">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="left-panel" style="margin-left: 20px; width:49%; float:left; display: inline;">
                        <div class="input-control text" data-role="input-control">
                            <input id="experiment-search" type="text">
                            <button class="btn-search"></button>
                        </div>

                        <div class="listview-outlook" data-role="listview">
                            <div id="experiment-list" class="list-group ">
                                <div id="select-experiment" class="group-content">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="clear: both;"></div>

                <div id="start-experiment-panel">
                    <div id="bottom-panels">
                        <div id="bottom-left-panel">
                            <h2 id="experiment-title"></h2>
                            <p id="experiment-info"></p>
                            <p id="experiment-text"></p>
                        </div>

                        <div id="bottom-right-panel">
                            <div id="input-fields"></div>
                            <div id="experiment-buttons"></div>
                        </div>
                    </div>
                </div>

            </div>
            <?php include_once("includes/footer.html"); ?>
        </div>
    </body>
</html>
