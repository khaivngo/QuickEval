<?php

// 23000 rader!
// fint at orginalen ikke ligger i picture order: siden den ikke kan markere uansett

require_once "../../classes/DB.php";

$experiment_id = $_POST["experiment_id"];

$experimentOwner = DB::instance()->run_query(
    "SELECT person FROM experiment WHERE id = ?",
    [$experiment_id]
)->get_results();

# get id of experiment queue
$experimentQueue = DB::instance()->run_query(
    "SELECT * FROM experimentqueue WHERE experiment = ?",
    [$experiment_id]
)->get_results();


# get IDs of picture queues by ID of experiment queue
$pictureQueues = DB::instance()->run_query(
    "SELECT * FROM experimentorder WHERE experimentqueue = ?",
    [$experimentQueue[0]->id]
)->get_results();


$pictures = [];
foreach ($pictureQueues as $pictureQueue) {
    $pictureOrder = DB::instance()->run_query(
        "SELECT * FROM pictureorder WHERE pictureQueue = ?",
        [$pictureQueue->pictureQueue]
    )->get_results();

    // array_push($pictureOrders, $pictureOrder);

    foreach ($pictureOrder as $picture) {
        $pic = DB::instance()->run_query(
            "SELECT * FROM picture WHERE id = ?",
            [$picture->picture]
        )->get_results();

        array_push($pictures, $pic[0]);
    }
}


# return the pictures to the ajax request
echo json_encode( [$pictures, $experimentOwner] );
exit;
