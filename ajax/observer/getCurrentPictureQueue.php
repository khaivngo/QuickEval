<?php

require_once "../../classes/DB.php";

$pictureID = $_POST['pictureID'];
$pictureOrderID = $_POST['pictureOrderID'];

$response = DB::instance()->run_query(
    "SELECT * FROM pictureorder WHERE id = ? AND picture = ?", [
        $pictureOrderID,
        $pictureID
    ]
)->get_results();

echo json_encode($response);
exit;

?>
