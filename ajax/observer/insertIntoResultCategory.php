<?php
/**
 * Will insert new results for category type experiments.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];             //fetching user id from session

try {
    
    $stmt = $db->prepare("INSERT INTO result (type, experimentId, pictureOrderId, personId, category) "
            . "VALUES (:type, :experimentId, :pictureOrderId, :personId, :category)");

    $stmt->execute(array(':type' => $_POST['type'],
        ':experimentId' => $_POST['experimentId'],
        ':pictureOrderId' => $_POST['pictureOrderId'],
        ':personId' => $userId,
        ':category' => $_POST['category'],
    ));
    
  //echo ($_POST['category']);  
} catch (Exception $ex) {
   // ChromePhp::log($ex->getMessage());
}
?>