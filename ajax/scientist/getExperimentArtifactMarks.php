<?php

require_once "../../classes/DB.php";

$experiment_id = $_POST["experiment_id"];
$picture_queue = $_POST["picture_queue"];
$picture_id = $_POST["picture_id"];

$response = DB::instance()->run_query(
    "SELECT * FROM artifactmark WHERE picture_id = ? AND picture_queue = ?",
    [$picture_id, $picture_queue]
)->get_results();

echo json_encode($response);
exit;

?>
