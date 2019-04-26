<?php
/**
 * Searches for a email and returns true if found.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
  $stmt = $db->prepare(
    "SELECT id FROM person " .
    "WHERE email=:email"
  );

  $stmt->execute(array(':email' => strtolower($_POST['email'])));
  $res = $stmt->rowCount();

  echo json_encode($res);
  exit;
} catch (PDOException $excpt) {
  // ChromePhp::log($excpt->getMessage());
}
?>