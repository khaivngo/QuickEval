<?php
/**
 * Gets all algorithmtypes.
 */

require_once('../../db.php');

try {
	$stmt = $db->query("SELECT * FROM algorithmtype");
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);
	exit;
} catch (Exception $ex) {
  //
}

?>