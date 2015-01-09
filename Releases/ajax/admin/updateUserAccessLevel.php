<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');


try {                                   //updates user's access level for the owner of email		
    $stmt = $db->prepare("UPDATE person, userType SET person.userType = userType.id WHERE person.email = :email AND userType.title  = :type");

    $stmt->execute(array(':email' => $_POST['email'],
        ':type' => $_POST['type']));
} catch (Exception $ex) {
    ChromePhp::log($ex->getMessage());
}
?>