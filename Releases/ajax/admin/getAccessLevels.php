<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    $stmt = $db->query("SELECT * FROM userType WHERE title <>  'Anonymous'");
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);

    
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>