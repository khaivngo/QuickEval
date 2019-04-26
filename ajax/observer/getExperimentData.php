<?php
/**
 * Will get infotypes for a given experiment.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
    if (isset($_POST['experiment'])) {  //Makes sure id is set
        $stmt = $db->prepare(
            "SELECT * FROM experiment e " .
            " WHERE e.id = ? " . ((isset($_POST['invite'])) ? ' AND inviteHash = ? ' : ' ' ) .
            " AND isPublic = '1 = Public'"
        );

        $stmt->bindParam(1, $_POST['experiment']);
        (isset($_POST['invite'])) ? $stmt->bindParam(2, $_POST['invite']) : '';
        $stmt->execute();
        $rows = $stmt->rowCount();

        if ($rows == 0) {               //Returns 0 if no rows are found
            $res[0] = 0;
        } else {
            $res[0] = $stmt->fetch();      //Fetches result
        }

        $stmt = $db->prepare(
            "SELECT *, infotype.id AS infoTypeId FROM infotype t " .
            " JOIN experimentinfotype e ON e.infoType = t.id " .
            " JOIN infotype ON e.infoType = infotype.id" .
            " WHERE e.experiment = ?"
        );

        $stmt->bindParam(1, $_POST['experiment']);
        $stmt->execute();

        $rows = $stmt->rowCount();

        if ($rows == 0) {
            $res[1] = 0;
        } else {
            $res[1] = $stmt->fetchAll();
        }
    } else {
        $res = 0; //Returns 0 if experiment is not set
    }

    echo json_encode($res);
    exit;
} catch (PDOException $excpt) {
    // ChromePhp::log($excpt->getMessage());
}
?>