<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

$stmt = $db->prepare("SELECT ps.*, p.url, p.name AS pictureName FROM pictureset ps " .
        "JOIN picture p ON ps.id = p.pictureSet " .
        "WHERE p.isOriginal=1 AND ps.person=:scientist");
$stmt->execute(array(':scientist' => $_SESSION['user']['id']));
$res = $stmt->fetchAll();

ChromePhp::log($res);

echo json_encode($res);
?>


