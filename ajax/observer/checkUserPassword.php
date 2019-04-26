<?php
/**
 * Will check user password.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
  $stmt = $db->prepare("SELECT id FROM person WHERE email=:email");

  $stmt->execute(array(':email' => $_POST['email']));
  $res = $stmt->rowCount();

  echo json_encode($res);
  exit;
} catch (PDOException $excpt) {
  // ChromePhp::log($excpt->getMessage());
}
?>