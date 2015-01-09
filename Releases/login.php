<!DOCTYPE html>
<?php session_start(); ?>
<html lang="en" style="height: 100%">
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
        <script src="js/sha3.js"></script> 
        <script src="js/loginScripts.js"></script> 

        <?php
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']['userType'] == 1) {
                header('Location: adminpanel.php');
            } elseif ($_SESSION['user']['userType'] == 2) {
                header('Location: scientistpanel.php');
            } else {
                header('Location: index.php');
            }
        }
        ?>

    </head>
    <body class="metro">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>
            <form>
                <div id="login-form" style="width:500px; margin:auto">
                    <div>
                        <h1>QuickEval</h1>
                    </div>

                    <fieldset>
                        <legend>Login</legend>
                        <label>Username</label>
                        <div class="input-control text" data-role="input-control">
                            <input id="email" type="text" placeholder="Username" autocomplete="on">
                            <button class="btn-clear" tabindex="-1"></button>
                        </div>
                        <label>Password</label>
                        <div id="password-div" class="input-control password" data-role="input-control">
                            <input id="password" type="password" placeholder="Password">
                            <button class="btn-reveal" tabindex="-1"></button>
                        </div>
                        <div style="margin-top: 20px">
                            <button id="submit" class="button primary">Login</button>

                        </div>
                        <legend style="margin-top: 20px"></legend>
                        <div style="margin-top: 20px">
                            <span>
                                Or log in as <strong>anonymous</strong>. This will not require a username or password, and options like gender
                                and nationality will not be saved. Information like browser, resolution and experiment results will
                                still be saved.
                            </span><br/>
                            <button style="margin-top:10px" id="anonymous" class="button default">Login Anonymous</button>
                        </div>
                    </fieldset>
                </div>
            </form>

            <?php include_once("includes/footer.html"); ?>
        </div>
    </body>
</html>