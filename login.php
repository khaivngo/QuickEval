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
    <script src="js/scripts.js"></script>
    <script src="js/loginScripts.js"></script>
    <?php echo (isset($_GET['redirect'])) ? '<input type="hidden" id="redirect" value="'. $_GET['redirect'] .'"/>'  : '' ?>

    <?php
    if (isset($_SESSION['user'])) {
        if(isset($_GET['redirect'])) {
            $url = 'Location: ' . $_GET['redirect'];
            header('Location: ' . $_GET['redirect']);
        } elseif ($_SESSION['user']['userType'] < 2) {
            header('Location: adminpanel.php');
        } elseif ($_SESSION['user']['userType'] == 2) {
            header('Location: scientistpanel.php');
        } elseif ($_SESSION['user']['userType'] > 2) {
            header('Location: index.php');
        }
    }
    ?>

</head>
<body class="metro">
    <div id="wrapper">
        <?php include_once("includes/headerLogin.html"); ?>
        <div style="background: url(images/hig.jpg) top left no-repeat; background-size: cover; height:300px;">
            <div style="width:500px; height: 100%; padding: 50px; background-color: #fff; background-color: rgba(0,0,0,0.5);">
                <h1 class="fg-white" style="font-weight: 550">QuickEval</h1>
                <h3 class="fg-white" style="font-weight: 300">QuickEval is a web-based tool for psychometric image evaluation. 
                    It supports rank order, paired comparison and category judgement. 
                    The tool is provided by the Norwegian Colour and Visual Computing Laboratory.</h3>

            </div>
        </div>



        <form class="form-remove" style="margin-top: 20px">
            <div id="login-form" style="width:500px; margin:auto">
                <fieldset>
                    <legend>Login</legend>
                    <label>Email</label>
                    <div class="input-control text" data-role="input-control">
                        <input id="email" type="text" placeholder="Email" autocomplete="on">
                    </div>
                    <label>Password</label>
                    <div id="password-div" class="input-control password" data-role="input-control">
                        <input id="password" type="password" placeholder="Password">
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