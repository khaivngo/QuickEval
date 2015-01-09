<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');


try {

    $type = $_POST['type'];

    if (isset($_POST['institute'])) {
	
        //If institute name is sent
        $institute = $_POST['institute'];
        $stmt = $db->prepare("SELECT * FROM workplace "
                . "WHERE LOWER(name) LIKE :institute AND "
                . "type = :type");

        $stmt->execute(array(':institute' => '%' . $institute . '%',
            ':type' => $type));
    } else {
        //If no data is sent
        $stmt = $db->prepare("SELECT * FROM workplace "
                . "WHERE type = :type "
                . "ORDER BY name ASC, RAND() "
                . "LIMIT 10");
				
        $stmt->execute(array(':type' => $type));
    }

    $rows = $stmt->rowCount();

    if ($rows == 0) {
        $res = 0;
    } else {
        $res = $stmt->fetchAll();
    }
		
	
    echo json_encode($res);
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>