<?php
/**
 * Will update email for logged in user.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
  //updates email for the particular user
  $stmt = $db->prepare("UPDATE person SET email =:email2 WHERE id = '" . $userId . "'");

  $stmt->execute(array(':email2' => $_POST['email2']));
} catch (Exception $ex) {
  // ChromePhp::log($excpt->getMessage());
}

?>