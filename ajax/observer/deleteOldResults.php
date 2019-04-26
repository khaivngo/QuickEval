<?php
/**
 * This file deletes all results that matches user and/or a chosen experiment.
 * Done to avoid having users that have taken the experiment multiple times,
 * resulting in multiple experiment-result sets.
 */

require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];
$check = $_POST['check'];

//Deletes results based on user and experiment
if ($check == 0) {
    try {
        $stmt = $db->prepare("DELETE FROM result WHERE personId = '" . $userId . "' AND experimentId = :eId");

        $stmt->execute(array(':eId' => $_POST['experimentId']));
        $res = $stmt->rowCount();

        echo json_encode($res);
        exit;
    } catch (PDOException $excpt) {
        //
    }

//Deletes all results for a given experiment
} else {
    try {
        $stmt = $db->prepare("DELETE FROM result WHERE experimentId = :eId");

        $stmt->execute(array(':eId' => $_POST['experimentId']));
        $res = $stmt->rowCount();

        echo json_encode($res);
        exit;
    } catch (PDOException $excpt) {
        //
    }
}

?>