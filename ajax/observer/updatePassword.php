<?php
/**
 * Will update passford for logged in user.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$email = $_SESSION['user']['email'];
$check = $_POST['check'];

if ($check == "newPassword") {
    try {
        //updates email for the particular user based on email
        $stmt = $db->prepare("UPDATE person SET password =:password WHERE email = '" . $email . "'");

        $stmt->execute(array(':password' => $_POST['password']));
    } catch (Exception $ex) {
        // 
    }
} else {
    try {
        //updates email for the particular user
        $stmt = $db->prepare("UPDATE person SET password =:password WHERE id = '" . $userId . "'");

        $stmt->execute(array(':password' => $_POST['password']));
    } catch (Exception $ex) {
        //
    }
}


?>