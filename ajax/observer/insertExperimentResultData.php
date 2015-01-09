<?php
/**
 * Will insert experimentResults to database.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];             //fetching user id from session

try {
    
    $stmt = $db->prepare("INSERT INTO experimentresult (os, xDimension, yDimension, experiment, person) "
            . "VALUES (:os, :xDimension, :yDimension, :experiment, :person)");

	
    $stmt->execute(array(':os' => $_POST['os'],
		':xDimension' => $_POST['xDimension'],
		':yDimension' => $_POST['yDimension'],
        ':experiment' => $_POST['experimentId'],
        ':person' => $userId,
    ));
    
} catch (Exception $ex) {
  //  ChromePhp::log($ex->getMessage());
}
?>