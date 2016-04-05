<?php

require_once "../../classes/DB.php";

$experiment_id = $_POST["experiment_id"];

$experimentOwner = DB::instance()->run_query(
    "SELECT person FROM experiment WHERE id = ?",
    [$experiment_id]
)->get_results();

# get all pictures for a specific experiment
$pictures = DB::instance()->run_query(
    "SELECT * FROM experimentqueue
    JOIN experimentorder
    ON experimentqueue.id = experimentorder.experimentqueue
    JOIN pictureorder
    ON experimentorder.picturequeue = pictureorder.picturequeue
    JOIN picture
    ON pictureorder.picture = picture.id
    WHERE experimentqueue.experiment = ?",
    [$experiment_id]
)->get_results();

# return the pictures to the ajax request
echo json_encode( [$pictures, $experimentOwner] );
exit;
