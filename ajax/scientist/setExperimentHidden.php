<?php
/**
 * Will change isPublic = 1 or 0 for a given experiment.
 */
require_once('../../db.php');

$userId = $_SESSION['user']['id'];
$experimentId = $_POST['experimentId'];
$hidden = $_POST['hidden'];

if ($_SESSION['user']['userType'] > 2) {
  exit;
}

try {
  # does the experiment exists and belong to the user
  $stmt = $db->prepare("SELECT id FROM experiment WHERE id = ? AND person = ?");
  $stmt->bindParam(1, $experimentId);
  $stmt->bindParam(2, $userId);
  $stmt->execute();
  $owner = $stmt->fetchAll();

  # if the user is the owner of the experiment
  if (!empty($owner)) {
  	$stmt = $db->prepare("UPDATE experiment SET isPublic = ? WHERE id = ? AND person = ?");
  	$stmt->bindParam(1, $hidden);
  	$stmt->bindParam(2, $experimentId);
  	$stmt->bindParam(3, $userId);
  	$stmt->execute();

  	echo json_encode(1);
    exit;
  } else {
    echo json_encode(0);
    exit;
  }
} catch (Exception $ex) {
  //
}

?>