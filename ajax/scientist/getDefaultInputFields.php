<?php
/**
 * Will get all standard infotypes.
 */

require_once('../../db.php');

if ($_SESSION['user']['userType'] > 2) {
	exit;
}

try	{
	$stmt = $db->query("SELECT * FROM infotype WHERE standardFlag = 1");
	$res = $stmt->fetchAll();
	echo json_encode($res);
  exit;
} catch (Exception $ex) {
  //
}

?>
