<?php
/**
 * Will get latest created experimentQueue ID for a given person.
 */
require_once('../../db.php');

try {
	$stmt = $db->prepare("SELECT experimentqueue.id FROM experimentqueue "
	. " JOIN experiment ON experimentqueue.experiment = experiment.id "
	. " WHERE experiment.person = :person"
	. " ORDER BY experimentqueue.id DESC");

	$stmt->execute(array(':person' => $_SESSION['user']['id']));
	$res = $stmt->fetch();
	
	echo json_encode($res);
  exit;
} catch (PDOException $excpt) {}

?>
