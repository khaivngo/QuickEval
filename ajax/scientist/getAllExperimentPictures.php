<?php

require_once "../../classes/Request.php";
require_once "../../classes/DB.php";

$experiment_id = Request::post('experiment_id');

$experimentOwner = DB::run(
    "SELECT person FROM experiment WHERE id = ?", [
        $experiment_id
    ]
);

# get all pictures for a specific experiment
$pictures = DB::run(
    "SELECT * FROM experimentqueue
    JOIN experimentorder
    ON experimentqueue.id = experimentorder.experimentqueue
    JOIN pictureorder
    ON experimentorder.picturequeue = pictureorder.picturequeue
    JOIN picture
    ON pictureorder.picture = picture.id
    WHERE experimentqueue.experiment = ?", [
        $experiment_id
    ]
);

# return the pictures to the ajax request
echo json_encode( [$pictures, $experimentOwner] );
exit;
