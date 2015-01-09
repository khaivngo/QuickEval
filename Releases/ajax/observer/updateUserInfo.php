<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];             //fetching user id from session

try {                                   //updates user info for the particular user		
    $stmt = $db->prepare("UPDATE person SET firstName =:firstName, lastName =:lastName, nationality =:nationality, title =:title, phoneNumber =:phoneNumber WHERE id = '" . $userId . "'");

    $stmt->execute(array(':firstName' => $_POST['firstName'],
        ':lastName' => $_POST['lastName'],
        ':nationality' => $_POST['nationality'],
        ':title' => $_POST['title'],
        ':phoneNumber' => $_POST['phoneNumber']));
} catch (Exception $ex) {
    ChromePhp::log($ex->getMessage());
}
?>