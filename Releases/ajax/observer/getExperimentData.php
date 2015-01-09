<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
    if (isset($_POST['experiment'])) {  //Makes sure id is set
        $stmt = $db->prepare("SELECT * FROM experiment e "
                . " WHERE e.id = :id "
                . " AND isPublic = 1");

        $stmt->execute(array(':id' => $_POST['experiment']));
        $rows = $stmt->rowCount();

        if ($rows == 0) {               //Returns 0 if no rows are found
            $res[0] = 0;
        } else {
            $res[0] = $stmt->fetch();      //Fetches result
        }

        $stmt = $db->prepare("SELECT * FROM infotype t "
                . " JOIN experimentinfotype e ON e.infoType = t.id "
                . " WHERE e.experiment = :id ");

        $stmt->execute(array(':id' => $_POST['experiment']));
        $rows = $stmt->rowCount();

        if ($rows == 0) {               //Returns 0 if no rows are found
            $res[1] = 0;
        } else {
            $res[1] = $stmt->fetchAll();      //Fetches result
        }
    } else {
        $res = 0;                       //Returns 0 if experiment is not set
    }

    ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>