<?php
/**
 * Will get all anonymous users from the database.
 * 
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');
if($_SESSION['user']['userType'] > 1) {
	return;
}
try {

    $stmt = $db->query("SELECT * FROM usertype WHERE title != 'Anonymous'");
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);

    
} catch (PDOException $excpt) {
   // ChromePhp::log($excpt->getMessage());
}
?>