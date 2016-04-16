<?php

/*****
 * Get all artifact marks associated with a specific picture.
 */

require_once "../../classes/Request.php";
require_once "../../classes/DB.php";

# Get the values from the AJAX request
$experiment_id = Request::post('experiment_id');
$picture_queue = Request::post('picture_queue');
$picture_id =    Request::post('picture_id');

# Get all the artifact marks for a specific picture
$artifact_marks = DB::run(
    "SELECT * FROM artifactmark WHERE picture_id = ? AND picture_queue = ?", [
        $picture_id,
        $picture_queue
    ]
);

# Return a json encoded database response
echo json_encode($artifact_marks);
exit;
