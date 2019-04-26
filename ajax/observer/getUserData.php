<?php
/**
 * Will get all user data from session
 */
session_start();

echo json_encode($_SESSION['user']);
exit;

?>
