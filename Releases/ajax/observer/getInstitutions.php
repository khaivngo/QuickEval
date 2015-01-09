<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

$stmt = $db->query("SELECT * FROM workplace");
$res = $stmt->fetchAll();

echo json_encode($res);
?>