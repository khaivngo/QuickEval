<?php
/**
 * This file is used to delete instruction from DB.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


$action = $_POST['action'];

if ($action == 0) {
    try {               //Inserts new inst or org into DB:
        $stmt = $db->prepare("INSERT INTO workplace (name, country, description, type) "
            . "VALUES (:name, :country, :description, :type)");

        $stmt->execute(array(':name' => $_POST['name'],
            ':country' => $_POST['country'],
            ':description' => $_POST['description'],
            ':type' => $_POST['type'],
        ));

        echo json_encode($stmt);
    } catch (Exception $ex) {
        echo $ex;
    }


} else if ($action == 1) {
    $array = json_decode($_POST['selection']);

    try {
//        var_dump($array);
        foreach ($array as $org) {
            $stmt = $db->prepare("DELETE FROM workplace WHERE id = '" . $org . ")'");
            $stmt->execute();

        }
        echo json_encode($stmt);

    } catch (Exception $ex) {

    }


}
?>