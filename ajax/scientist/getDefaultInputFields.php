<?php
/**
 * Will get all standard infotypes.
 */
require_once('../../db.php');
if($_SESSION['user']['userType'] > 2) {
	return;
}

try	{
                                  //updates user's access level for the owner of email		
	$stmt = $db->query("SELECT * FROM infotype WHERE standardFlag = 1");
	$res = $stmt->fetchAll();
	echo json_encode($res);

} catch (Exception $ex) {
}
?>