<?php
/**
 * Insert a artifact mark into the database.
 */
require_once "../../db.php";

$picture_queue = isset($_POST['picture_queue']) ? $_POST['picture_queue'] : 0;
$experiment_id = isset($_POST['experiment_id']) ? $_POST['experiment_id'] : 0;
         $mark = isset($_POST['mark']) ? $_POST['mark'] : 0;
       $remark = isset($_POST['remark']) ? $_POST['remark'] : "";
      $user_id = $_SESSION['user']['id'];

$stmt = $db->prepare(
  "INSERT INTO artifactmark (picture_queue, experiment_id, marked_pixels, remark, person)
   VALUES (?, ?, ?, ?, ?)"
);

$stmt->bindParam(1, $picture_queue);
$stmt->bindParam(2, $experiment_id);
$stmt->bindParam(3, $mark);
$stmt->bindParam(4, $remark);
$stmt->bindParam(5, $user_id);
$stmt->execute();
