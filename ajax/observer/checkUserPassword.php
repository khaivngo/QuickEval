<?php
/**
 * Will check user password.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];              //fetching user id from session

try {

    //---------------------------REGISTER PERSON---------------------------------//

    $stmt = $db->prepare("SELECT id FROM person "
            . "WHERE email=:email");

    $stmt->execute(array(':email' => $_POST['email']));
    $res = $stmt->rowCount();
    
 //   ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
  //  ChromePhp::log($excpt->getMessage());
}
?>