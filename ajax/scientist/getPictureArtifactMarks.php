<?php

/*****
 * Get all artifact marks associated with a specific picture.
 */
require_once "../../db.php";

$experiment_id = isset($_POST['experiment_id']) ? $_POST['experiment_id'] : 0;
$picture_queue = isset($_POST['picture_queue']) ? $_POST['picture_queue'] : 0;

$stmt = $db->prepare("SELECT * FROM artifactmark WHERE picture_queue = ?");
$stmt->bindParam(1, $picture_queue);
$stmt->execute();
$artifact_marks = $stmt->fetchAll(PDO::FETCH_OBJ);

# Return a json encoded database response
echo json_encode($artifact_marks);
exit;
