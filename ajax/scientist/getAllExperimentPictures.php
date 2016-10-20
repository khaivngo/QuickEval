<?php

require_once "../../db.php";

$experiment_id = isset($_POST['experiment_id']) ? $_POST['experiment_id'] : 0;

$stmt = $db->prepare("SELECT person FROM experiment WHERE id = ?");
$stmt->bindParam(1, $experiment_id);
$stmt->execute();
$experimentOwner = $stmt->fetchAll(PDO::FETCH_OBJ);


# get all pictures for a specific experiment
$stmt2 = $db->prepare(
    "SELECT pictureorder.id AS pictureorder,
    picture.id AS pictureid, picture.name AS picturename, picture.url AS pictureurl, picture.pictureSet AS pictureSet
    FROM experimentqueue
    JOIN experimentorder
    ON experimentqueue.id = experimentorder.experimentqueue
    JOIN pictureorder
    ON experimentorder.picturequeue = pictureorder.picturequeue
    JOIN picture
    ON pictureorder.picture = picture.id
    WHERE experimentqueue.experiment = ?"
);

$stmt2->bindParam(1, $experiment_id);
$stmt2->execute();
$pictures = $stmt2->fetchAll(PDO::FETCH_OBJ);


# return the pictures to the ajax request
echo json_encode( [$pictures, $experimentOwner] );
exit;
