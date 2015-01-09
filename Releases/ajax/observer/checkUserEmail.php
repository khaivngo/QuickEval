<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {


    $stmt = $db->prepare("SELECT id FROM person "
            . "WHERE email=:email");

    $stmt->execute(array(':email' => strtolower($_POST['email'])));
    $res = $stmt->rowCount();
    
    ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>