<?php

require_once "../../classes/DB.php";

$experiment_id = $_POST["experiment_id"];
$picture_queue = $_POST["picture_queue"];

$response = DB::instance()->run_query(
    "SELECT * FROM artifactmark WHERE experiment_id = ? AND picture_queue = ?",
    [$experiment_id, $picture_queue]
)->get_results();

echo json_encode($response);
exit;

?>
