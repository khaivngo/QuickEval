<?php

/*****
 * Insert a artifact mark conducted by a user.
 */
session_start();
require_once "../../classes/Request.php";
require_once "../../classes/DB.php";

DB::run(
    "INSERT INTO artifactmark (picture_id, picture_queue, experiment_id, marked_pixels, remark, person) VALUES (?, ?, ?, ?, ?, ?)", [
        Request::post('picture_id'),
        Request::post('picture_queue'),
        Request::post('experiment_id'),
        Request::post('mark'),
        Request::post('remark'),
        $_SESSION['user']['id']
    ]
);
