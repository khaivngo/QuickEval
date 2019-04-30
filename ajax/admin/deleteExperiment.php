<?php
/**
 * Will delete an experiment based on experimentId and userId
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];
$experimentId = $_POST['experimentId'];

try {
  $sql = "DELETE FROM experiment WHERE id = ? AND person = ?";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(1, $experimentId);
  $stmt->bindParam(2, $userId);
  $stmt->execute();
  $affectedRows = $stmt->rowCount();

  // if successfull delete exactly one db table row should be affected
  if ($affectedRows === 1) {
    echo json_encode(1);
    exit;
  } else {
    echo json_encode(0);
    exit;
  }
} catch (Exception $ex) {
   // ChromePhp::log($ex->getMessage());
}

?>