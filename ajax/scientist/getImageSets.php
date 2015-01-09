<?php
/**
 * Will get ALL imagesets for a given person.
 */
require_once('../../db.php');

$stmt = $db->prepare("SELECT ps.*, p.url, p.name AS pictureName FROM pictureset ps " .
        "JOIN picture p ON ps.id = p.pictureSet " .
        "WHERE p.isOriginal=1 AND ps.person=:scientist");
$stmt->execute(array(':scientist' => $_SESSION['user']['id']));
$res = $stmt->fetchAll();
echo json_encode($res);
?>


