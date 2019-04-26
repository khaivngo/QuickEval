<?php
/**
 * Will change isPublic = 1 or 0 for a given experiment.
 */
require_once('../../db.php');
require_once('functions.php');

$userId = $_SESSION['user']['id'];
$experimentId = $_POST['experimentId'];
$hidden = $_POST['hidden'];

if (checkLogin > 2) {
  exit;
}

try {
	$stmt = $db->prepare("UPDATE experiment SET isPublic = ? WHERE id = ? AND person = ? ");
	$stmt->bindParam(1, $hidden);
	$stmt->bindParam(2, $experimentId);
	$stmt->bindParam(3, $userId);
	$stmt->execute();
	
	echo json_encode(1);
  exit;
} catch (Exception $ex) {
  //
}

?>