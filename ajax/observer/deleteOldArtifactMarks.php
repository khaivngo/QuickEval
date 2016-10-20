<?php

/*****
 * Delete all artifact marks belonging to a experiment.
 */
require_once "../../db.php";


$experiment_id = isset($_POST['experimentId']) ? $_POST['experimentId'] : 0;
$user_id = $_SESSION['user']['id'];

$stmt = $db->prepare("DELETE FROM artifactmark WHERE experiment_id = ? AND person = ?");
$stmt->bindParam(1, $experiment_id);
$stmt->bindParam(2, $user_id);
$stmt->execute();

echo json_encode('Deleted.');
exit;
