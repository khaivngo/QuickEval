<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
    if (isset($_POST['fieldValues']) && isset($_POST['experiment'])) {  //Makes sure id is set
        $e = $_POST['experiment'];
        
        $stmt = $db->prepare("INSERT INTO infofield"
            . "(infoType, text, experiment, person) "
            . "VALUES(:infoType, :text, :experiment, :person) ");
        
        foreach($_POST['fieldValues'] as $field){
            $stmt->execute(array(':infoType' => $field[0],
                ':text' => $field[1],
                ':experiment' => $e,
                ':person' => $_SESSION['user']['id']));
        }
        $res = 1;
        
    } else {
        $res = 0;                       //Returns 0 if experiment is not set
    }

    ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>