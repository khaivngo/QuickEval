<?php
/**
 * Checks if a person has completed an experiment.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
	//returns id if user has taken any experiment
	$stmt = $db->prepare("SELECT id from experimentResult WHERE person = '" . $userId . "'  AND experiment = :experimentId ");

  $stmt->execute(array(':experimentId' => $_POST['experimentId']));

	$res = $stmt->fetchAll();

	echo json_encode($res);
	exit;
} catch (Exception $excpt) {
  // ChromePhp::log($excpt->getMessage());
}

?>