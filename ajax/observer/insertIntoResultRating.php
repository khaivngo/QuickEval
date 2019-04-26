<?php
/**
 * Will add new results to the database.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
    $stmt = $db->prepare(
      "INSERT INTO result (type, experimentId, pictureOrderId, personId) " .
      "VALUES (:type, :experimentId, :pictureOrderId, :personId)"
    );

    $stmt->execute(array(
      ':type' => $_POST['type'],
      ':experimentId' => $_POST['experimentId'],
      ':pictureOrderId' => $_POST['pictureOrderId'],
      ':personId' => $userId,
    ));
} catch (Exception $ex) {
    //ChromePhp::log($ex->getMessage());
}

?>