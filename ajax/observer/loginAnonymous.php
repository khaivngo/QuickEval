<?php
/**
 * Will create a new anomynous user, and log him/her in.
 */
require_once('../../db.php');

try {
    $stmt = $db->prepare("SELECT * FROM person WHERE userType = '4' ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $res = $stmt->fetch();
    $id = substr($res['firstName'], 9) + 1;


    //-----------LOGIN PERSON------------//
    $stmt = $db->prepare("INSERT INTO person (firstName, userType) VALUES(:firstName, '4')");
    $stmt->execute(array(':firstName' => "Anonymous" . ($id)));

    $stmt = $db->prepare("SELECT * FROM person WHERE firstName = :firstName AND userType = '4'");
    $stmt->execute(array(':firstName' => 'Anonymous' . ($id)));
    
    $rows = $stmt->rowCount();

    if ($rows == 1) {
        $res = $stmt->fetchAll();
        $_SESSION['user'] = $res[0];

        echo json_encode($res);
        exit;
    } else {
        echo json_encode("0");
        exit;
    }
} catch (PDOException $excpt) {
    //
}

?>