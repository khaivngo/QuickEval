<?php
/**
 * Gets information on experiment if you want to show timer.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


try {                                   
    $stmt = $db->prepare("Select timer FROM experiment WHERE id = :id");

    $stmt->execute(array(':id' => $_POST['experimentId']));
	$res = $stmt->fetchAll();
	
	echo json_encode($res);
	
} catch (Exception $ex) {
 //  ChromePhp::log($excpt->getMessage());
}

?>