<?php
/**
 * Checks if a invite code is correct.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
    $stmt = $db->prepare("SELECT id FROM experiment WHERE inviteHash=:invite");

    $stmt->execute(array(':invite' => $_POST['invite']));
    $res = $stmt->fetch();

    echo json_encode($res['id']);
    exit;

} catch (PDOException $excpt) {
    // ChromePhp::log($excpt->getMessage());
}

?>
