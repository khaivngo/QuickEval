<?php
/**
* Gets results for an pair-experiment
*/

require_once('../../db.php');
require_once('../../functions.php');

if (!isset($_SESSION['user'])) {
	header("Location: ../../login.php"); 
}	

try {

	$experimentId = $_POST['experimentId'];
	$data = getExperimentById($experimentId, $db);

	echo json_encode($data);

} catch(PDOException $excpt) {
	echo json_encode("0");
}
?>