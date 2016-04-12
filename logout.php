<?php
    session_start();
    session_destroy();

    // In order to kill the session altogether, like to log the user out, the session id must also be unset.
    unset($_SESSION["user"]);

    header("Location: login.php");
    exit;
?>
