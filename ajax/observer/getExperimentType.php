<?php

/**
 * WIll get experimentType for a given experimentId
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    $stmt = $db->prepare("SELECT type FROM experimenttype as a WHERE EXISTS (SELECT * FROM experiment as b WHERE b.id = :experimentId AND b.experimenttype = a.id)");
	
	$stmt->execute(array(':experimentId' => $_POST['experimentId']));
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);
    
} catch (PDOException $excpt) {
   // ChromePhp::log($excpt->getMessage());
}
?>