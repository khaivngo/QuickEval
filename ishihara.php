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
        <script src="js/ishihara.js"></script>
        <script src="js/scripts.js"></script>


    </head>


    <?php session_start(); ?>


    <body class="metro">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>

            <div id="ishihara-form" style="width:500px; margin:auto">
                <div>
                    <br>
                    <h1>Ishihara Colour test</h1>
                </div>
                <div class="grid">
                    <div class="row">
                        <div class="span3" style="margin-left:-30px;">
                            <img src="images/ishihara/12Plate1.png" alt="Ishihara test plate 1">
                        </div>
                        <div class="span3">
                            <img src="images/ishihara/5Plate4.png" alt="Ishihara test plate 2">
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="row">
                                    <div class="row">
                                        <div class="row">
                                            <div class="row">
                                                <div class="row">
                                                    <div class="row">
                                                        <div class="row">
                                                            <div class="row">
                                                                <div class="row">
                                                                    <div class="span2">
                                                                        <div class="input-control text">
                                                                            <input id="picture1" type="text" value="" placeholder="Palet number"/>
                                                                            <button class="btn-clear"></button>
                                                                        </div>
                                                                    </div>
                                                                     <div class ="offset3">
                                                                    <div class="span2">
                                                                        <div class="input-control text">
                                                                            <input id="picture2" type="text" value="" placeholder="Palet number"/>
                                                                            <button class="btn-clear"></button>
                                                                        </div>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="span3"style="margin-left:-30px;"><img src="images/ishihara/8Plate2.png" alt="Ishihara test plate 3"> </div>
                            <div class="span3"><img src="images/ishihara/26Plate16.png" alt="Ishihara test plate 4"> </div>
                            <div class="row">
                                <div class="row">
                                    <div class="row">
                                        <div class="row">
                                            <div class="row">
                                                <div class="row">
                                                    <div class="row">
                                                        <div class="row">
                                                            <div class="row">
                                                                <div class="row">
                                                                    <div class="row">
                                                                        <div class="row">
                                                                            <div class="span2" >
                                                                                <div class="input-control text">
                                                                                    <input id="picture3" type="text" value="" placeholder="Palet number"/>
                                                                                    <button class="btn-clear"></button>
                                                                                </div>
                                                                            </div>
                                                                            <div class ="offset3">
                                                                                <div class="span2">
                                                                                    <div class="input-control text">
                                                                                        <input id="picture4" type="text" value="" placeholder="Palet number"/>
                                                                                        <button class="btn-clear"></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="offset5">
                    <div id="submit-div" style="margin-top: 20px">
                        <button id="submit" class="button primary" style="margin:20px ">Submit</submit>
                    </div>
                </div>

                </body>
                <?php include_once("includes/footer.html"); ?>
                </html>
