<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    $stmt = $db->prepare("SELECT * FROM person "
            . "WHERE userType = '4' ORDER BY id DESC LIMIT 1 ");
    $stmt->execute();

    $res = $stmt->fetch();
    $id = substr($res['firstName'], 9) + 1;
    ChromePhp::log($id);
    //---------------------------LOGIN PERSON---------------------------------//

    $stmt = $db->prepare("INSERT INTO person"
            . "(firstName, userType) "
            . "VALUES(:firstName, '4') ");

    $stmt->execute(array(':firstName' => "Anonymous" . ($id)));

    ChromePhp::log("after insert");
    $stmt = $db->prepare("SELECT * FROM person "
            . "WHERE firstName = :firstName AND userType = '4'");
    $stmt->execute(array(':firstName' => 'Anonymous' . ($id)));
    $rows = $stmt->rowCount();
    if ($rows == 1) {
        $res = $stmt->fetchAll();
        $_SESSION['user'] = $res[0];

        ChromePhp::log($_SESSION['user']['firstName']);

        echo json_encode($res);
    } else {
        echo json_encode("0");
    }
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>