<?php
/**
 * Will colorblondflag for logged in user.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];

try {
  //updates users colourblindness
  $stmt = $db->query("UPDATE person SET colourBlindFlag = '1' WHERE id = '" . $userId . "'");
  $_SESSION['user']["colourBlindFlag"] = 1;

} catch (Exception $excpt) {
  // ChromePhp::log($excpt->getMessage());
}

?>