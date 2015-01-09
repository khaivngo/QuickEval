<?php
/**
 * Gets all algorithmtypes.
 */
require_once('../../db.php');


try {                                   //fetches all available algorithm types from database		
	$stmt = $db->query("SELECT * FROM algorithmtype");
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);		//returns them to ajax
							
} catch (Exception $ex) {
}
?>