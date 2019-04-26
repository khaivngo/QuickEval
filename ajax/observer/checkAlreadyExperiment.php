<?php
/**
 * Will check if a person exists based on email.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
  $stmt = $db->prepare(
    "SELECT DISTINCT experimentId FROM result " .
    "WHERE personId = '" . $userId . "' AND experimentId = :eId"
  );

  $stmt->execute(array(':eId' => $_POST['experimentId']));
  $res = $stmt->rowCount();

  echo json_encode($res);
  exit;
} catch (PDOException $excpt) {
  // ChromePhp::log($excpt->getMessage());
}

?>