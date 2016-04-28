<?php
/**
 * Gets specific data about a given experiment.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


try {                                   //gets whether the experiment is hidden
    $stmt = $db->prepare("SELECT isPublic, backgroundColour, showOriginal, timer, allowArtifactMarking FROM experiment WHERE id = :id");

    $stmt->execute(array(':id' => $_POST['experimentId']));
	$res = $stmt->fetchAll();

	echo json_encode($res);
    exit;

} catch (Exception $excpt) {
    // ChromePhp::log($excpt->getMessage());
}

?>
