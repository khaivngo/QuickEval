<?php
/**
 * Will check if a person exists based on email.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
  $stmt = $db->prepare(
    "SELECT id FROM person " .
    "WHERE email=:email2"
  );

  $stmt->execute(array(':email2' => $_POST['email2']));
  $res = $stmt->rowCount();

  echo json_encode($res);
  exit;
} catch (PDOException $excpt) {
  //
}
?>