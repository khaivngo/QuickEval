<?php
/**
 * This file is used to delete instruction from DB.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


$array=json_decode($_POST['selection']);


    try {
//        var_dump($array);
        foreach($array as $instruction) {
            $stmt = $db->prepare("DELETE FROM instruction WHERE id = '". $instruction . ")'");
            $stmt->execute();
        }

    } catch (Exception $ex) {

    }

?>