<?php
/**
 * This file is used to delete instruction from DB.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$value = $_POST['orgValue'];

try {
    $stmt = $db->prepare("DELETE FROM workplace WHERE id = '" . $value . ")'");
    $stmt->execute();
    $res = $stmt->rowCount();
    echo json_encode($res);
    exit;
} catch (Exception $ex) {
  // 
}

?>