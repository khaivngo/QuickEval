<!DOCTYPE html>
<?php
    session_start();

    if($_SESSION['user']['userType'] > 3) {
        header('Location: index.php');
        exit;
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

        <!-- JQuery -->
        <script src="js/jquery/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- Metro UI -->
        <script src="min/metro.min.js"></script>

        <!-- Other JS -->
        <script src="js/userProfileScripts.js"></script>
        <script src="js/scripts.js"></script>
		<script src="js/sha3.js"></script>

		<script type="text/javascript">
			window.onload = fillUserInfo();
		</script>


    </head>
    <body class="metro" onload="fillUserInfo();">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>

            <div id="panels" style="width:1200px; margin:auto">
                <div id="left-panel" style="float:left">
                    <div class="span4" >
                        <nav class="sidebar light" >
                            <ul>
                                <li class="title">Profile</li>
                                <li id="edit-info" class="active stick bg-green"><a  href="#"><i class="icon-database"></i>Edit Info</a></li>
                                <li class="title">Settings</li>
                                <li id="change-password"><a href="#"><i class="icon-undo"></i>Change Password</a></li>
                                <li id="change-email" ><a href="#"><i class="icon-mail"></i>Change Email</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div id="right-panel" style="float: left; margin:20px; width: 49%;">
                </div>

            </div>
            <?php include_once("includes/footer.html"); ?>
        </div>
    </body>
</html>
