<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];              //fetching user id from session

try {               //updates users colourblindness
    $stmt = $db->query("UPDATE person SET colourBlindFlag = 1 WHERE id = '" . $userId . "'");

    
} catch (Exception $excpt) {
    ChromePhp::log($excpt->getMessage());
    
}
?>