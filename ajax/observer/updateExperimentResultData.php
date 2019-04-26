<?php
/**
 * Will update experimentresults for logged in person
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
  $stmt = $db->prepare(
    'UPDATE experimentresult SET endTime = :endTime, complete = "1" WHERE person = "' . $userId .
    '" AND experiment = :experiment ORDER BY id DESC LIMIT 1;'
  );

  ChromePhp::log($_POST['endTime']);

  $stmt->execute(array(':endTime' => $_POST['endTime'],
      ':experiment' => $_POST['experimentId']
  ));
  
  echo $_POST['experimentId'];
  exit;
} catch (Exception $ex) {
  // 
}

?>