<?php
/**
 * This file deletes all results that matches user and a chosen experiment.
 * Done to avoid having users that have taken the experiment multiple times.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];              //fetching user id from session


try {
    $stmt = $db->prepare("DELETE FROM result "
        . "WHERE personId = '" . $userId . "' AND experimentId = :eId");

    $stmt->execute(array(':eId' => $_POST['experimentId']));
    $res = $stmt->rowCount();

//    ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
    //   ChromePhp::log($excpt->getMessage());
}
?>