<?php

session_start();
$db = new PDO(
        'mysql:host=127.0.0.1;' .
        'dbname=db_quickeval;', 'root', 'mysql');

$db->setAttribute(PDO::ATTR_ERRMODE, 
        PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 
        "SET NAMES utf8");
		
$db->query("SET NAMES 'utf8'");			//ensures what client is sending to server and respond from server is encoded in UTF-8 
?>
