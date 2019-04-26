<?php
/**
 * Will insert chosen picture into resulsts
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];
$pictureOrderId = $_POST['pictureOrderId'];

//Sets value of variable when user has chosen none of the reproductions so query is able to execute.
if ($_POST['chooseNone'] == 'null') {
    $choose = NULL;
} else {
    $choose = 1;
}

if (empty($pictureOrderId)){
    exit;
}

try {
    //post results about which is chosen to DB.    
    $stmt = $db->prepare(
        "INSERT INTO result (type, experimentId, pictureOrderId, chooseNone, personId) " .
        "VALUES (:type, :experimentId, :pictureOrderId, :chooseNone, :personId)"
    );

    $stmt->execute(array(':type' => $_POST['type'],
        ':experimentId' => $_POST['experimentId'],
        ':pictureOrderId' => $pictureOrderId,
        ':personId' => $userId,
        ':chooseNone' => $choose,
    ));
} catch (Exception $ex) {
   // ChromePhp::log($ex->getMessage());
}

?>