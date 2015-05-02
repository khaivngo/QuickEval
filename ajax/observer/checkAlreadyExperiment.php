<?php
/**
 * Will check if a person exists based on email.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];              //fetching user id from session


try {

    $stmt = $db->prepare("SELECT DISTINCT experimentId FROM result "
            . "WHERE personId = '" . $userId . "' AND experimentId = :eId");

    $stmt->execute(array(':eId' => $_POST['experimentId']));
    $res = $stmt->rowCount();

//    ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
 //   ChromePhp::log($excpt->getMessage());
}
?>