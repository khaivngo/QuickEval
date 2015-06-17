<?php
/**
 * This file deletes all results that matches user and/or a chosen experiment.
 * Done to avoid having users that have taken the experiment multiple times,
 * resulting in multiple experiment-result sets.
 */

require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];              //fetching user id from session
$check = $_POST['check'];

if ($check == 0) {       //Deletes results based on user and experiment
    try {
        $stmt = $db->prepare("DELETE FROM result "
            . "WHERE personId = '" . $userId . "' AND experimentId = :eId");

        $stmt->execute(array(':eId' => $_POST['experimentId']));
        $res = $stmt->rowCount();

        echo json_encode($res);
    } catch (PDOException $excpt) {

    }

} else {          //Deletes all results for a given experiment
    try {
        $stmt = $db->prepare("DELETE FROM result WHERE experimentId = :eId");

        $stmt->execute(array(':eId' => $_POST['experimentId']));
        $res = $stmt->rowCount();

        echo json_encode($res);
    } catch (PDOException $excpt) {

    }
}
?>