<?php
/**
 * Updates a users access level with the help of his/hers email.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

if($_SESSION['user']['userType'] > 1) {
	return;
}
try {                                   //updates user's access level for the owner of email		
    $stmt = $db->prepare("UPDATE person, userType SET person.userType = usertype.id WHERE person.email = :email AND usertype.title  = :type");

    $stmt->execute(array(':email' => $_POST['email'],
        ':type' => $_POST['type']));
} catch (Exception $ex) {
   // ChromePhp::log($ex->getMessage());
}
?>