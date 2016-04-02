<?php

require_once "../../classes/DB.php";

$experiment_id = $_POST["experiment_id"];

$response = DB::instance()->run_query(
    "SELECT * FROM artifactmark WHERE experiment_id = ?",
    [$experiment_id]
)->get_results();

echo json_encode($response);
exit;

?>
