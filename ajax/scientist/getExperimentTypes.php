<?php
/**
 * Will get all existing experimenttypes
 */
require_once('../../db.php');

try {
	$stmt = $db->query("SELECT * FROM experimenttype");
	
	$res = $stmt->fetchAll();
	
	echo json_encode($res);
  exit;
} catch (Exception $ex) {
}

?>