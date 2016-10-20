<!DOCTYPE html>
<?php 
    session_start();  
    if(isset($_SESSION['user']['id'])) {
        if($_SESSION['user']['userType'] > 1) {
            header('Location: index.php');
        }
    } else {
        header('Location: index.php');
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
        <script src="js/admin/adminScripts.js"></script>      
        <script src="js/admin/adminPanelScripts.js"></script>
        <script src="js/scripts.js"></script>		

		
		
		
    </head>
    <body class="metro">
        <div id="wrapper">
            <?php include_once("includes/header.html"); ?>

            <div id="panels" style="width:1200px; margin:auto; margin-bottom: 560px;">
                <div id="left-panel" class="span4" style="float:left">
                    <nav class="sidebar light" >
                        <ul>
                            <li class="title">Admin Panel</li>
                            <li id="dashboard" class="active stick bg-green"><a  href="#"><i class="icon-home"></i>Dashboard</a></li>

                            <li class="title">Images</li>
                            <li id="delete-images"><a href="#"><i class="icon-remove"></i>Delete Images</a></li>

                            <li class="title">Users</li>
                            <li id="delete-anonymous" ><a href="#"><i class="icon-cycle"></i>Clean Up Anonymous Users</a></li>
                            <li id="change-access" ><a href="#"><i class="icon-user-3"></i>Change User Access</a></li>
                            
                            <li class="title">Experiments</li>
                            <li id="delete-experiments" ><a href="#"><i class="icon-remove"></i>Delete Experiments</a></li>
                            <li id="delete-instruction" ><a href="#"><i class="icon-paragraph-justify"></i>Manage Instructions</a></li>

                            <li class="title">Organization/Institute</li>
                            <li id="manage-org" ><a href="#"><i class="icon-paragraph-justify"></i>Add/Remove</a></li>
                            <li id="edit-org" ><a href="#"><i class="icon-pencil"></i>Edit</a></li>

                            <li class="title">Settings</li>
                            <li id="adm-settings" ><a href="#"><i class="icon-wrench"></i>Settings</a></li>

                        </ul>
                    </nav>
                </div>
                <div id="right-panel" style="float: left; margin:20px; width: 60%">
                </div>

            </div>
            <?php include_once("includes/footer.html"); ?>
        </div>
    </body>
</html>