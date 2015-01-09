<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];              //fetching user id from session

try {                                   //updates email for the particular user
    $stmt = $db->prepare("UPDATE person SET email =:email2 WHERE id = '" . $userId . "'");

    $stmt->execute(array(':email2' => $_POST['email2']));

    ChromePhp::log("Update complete");          //for logging purposes
} catch (Exception $ex) {
   ChromePhp::log($excpt->getMessage());
}

?>