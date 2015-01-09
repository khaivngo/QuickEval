<?php
/**
 * Will get all existing experimenttypes
 */
require_once('../../db.php');


try {                                   //fetches all available experiment types from database		
	$stmt = $db->query("SELECT * FROM experimenttype");
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);		//returns them to ajax
							
} catch (Exception $ex) {
}
?>