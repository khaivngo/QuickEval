<?php
/**
 * Will update passford for logged in user.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$email = $_SESSION['user']['email'];           //fetching user id from session
$check = $_POST['check'];

//ChromePhp::log($_POST['email']);
//ChromePhp::log($_SESSION['user']['email']);


if ($check == "newPassword") {
//    ChromePhp::log('New password!');
    try {                                   //updates email for the particular user based on email
        $stmt = $db->prepare("UPDATE person SET password =:password WHERE email = '" . $email . "'");

        $stmt->execute(array(':password' => $_POST['password']));

    } catch (Exception $ex) {
    }
} else {
    try {                                   //updates email for the particular user
        $stmt = $db->prepare("UPDATE person SET password =:password WHERE id = '" . $userId . "'");

        $stmt->execute(array(':password' => $_POST['password']));

    } catch (Exception $ex) {
    }
}


?>