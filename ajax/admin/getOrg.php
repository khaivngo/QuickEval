<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
    $stmt = $db->prepare("SELECT name, id, country, description, type FROM workplace WHERE name != 'NULL'");

    $stmt->execute();
    $res = $stmt->fetchAll();
    echo json_encode($res);

} catch (PDOException $excpt) {}

?>