<?php
/**
 * Will check for a scientist(or scientists) that matches the first or lastname.  Used in a search function.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    if (isset($_POST['scientist'])) {

        //If institute name is sent

        $scientist = $_POST['scientist'];

        $stmt = $db->prepare("SELECT * FROM person "
                . "WHERE (LOWER(firstName) LIKE :scientist OR "
                . "LOWER(lastName) LIKE :scientist) AND "
                . "userType <= 2");

        $stmt->execute(array(':scientist' => '%' . $scientist . '%'));
    }

    $rows = $stmt->rowCount();

    if ($rows == 0) {
        $res = 0;
    } else {
        $res = $stmt->fetchAll();
	
    }
	
    echo json_encode($res);
} catch (PDOException $excpt) {
   // ChromePhp::log($excpt->getMessage());
}
?>