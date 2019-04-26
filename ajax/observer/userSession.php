<?php
/**
 * Will log in a person and update his/hers session
 */
require_once('../../db.php');

try {
    if (isset($_POST['email'])) {
        // LOGIN PERSON
        $stmt = $db->prepare("SELECT * FROM person WHERE email=:email AND password=:password");
        $stmt->execute(array(
            ':email' => strtolower($_POST['email']),
            ':password' => $_POST['password']
        ));

    } elseif (isset($_SESSION['user'])) {
        //UPDATES SESSION
        $stmt = $db->prepare("SELECT * FROM person WHERE id=:id");
        $stmt->execute(array(':id' => $_SESSION['user']['id']));
    }

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
    echo json_encode("0");
    exit;
}

?>