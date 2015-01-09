<?php
/**
 * Gets all institutions
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


try	{
	$stmt = $db->query("SELECT * FROM workplace");
	$res = $stmt->fetchAll();

	echo json_encode($res);

}catch(Exception $ex)	{

}

?>