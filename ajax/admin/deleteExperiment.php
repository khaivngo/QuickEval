<?php
/**
 * Will delete an experiment based on experimentID and usedId
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$userId = $_SESSION['user']['id'];             //fetching user id from session
$experimentId = $_POST['experimentId'];

try {
    $stmt = $db->exec("DELETE FROM experiment WHERE id = '" . $experimentId . "' AND person = '" . $userId . "'");

   // $stmt->execute(array(':id' => $_POST['experimentId'],
	//));

    require_once "../../classes/DB.php";

    DB::run_query(
        "DELETE FROM artifactmark WHERE experiment_id = ?", [
            $experimentId
        ]
    );

	echo json_encode($stmt);
    exit;

} catch (Exception $ex) {
   // ChromePhp::log($ex->getMessage());
}


?>
