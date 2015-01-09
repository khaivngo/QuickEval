<!DOCTYPE html>
<?php
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!-- CSS -->
        <link href="css/metro-bootstrap.css" rel="stylesheet">
        <link href="css/jquery/ui-lightness/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

        <!-- JQuery -->
        <script src="js/jquery/jquery.min.js"></script>
        <script src="js/jquery/jquery-ui.custom.min.js"></script>

        <!-- Metro UI -->
        <script src="min/metro.min.js"></script>


        <!-- Other JS -->
        <script src="js/plugins/jquery.panzoom.min.js"></script>


    </head>

    <body class="metro" style="background-color:#808080;" >

        <div id="container" style="text-align:center; vertical-align:text-top; margin-top:2%;">
            <img id="image" src="">


        </div>

    </body>


    <script type="text/javascript">
        var url = window.data;
        var colour = window.colour;
        
        $('#image').attr('src', url);
        $('body').css("background-color", colour);
        
    </script>
</html>