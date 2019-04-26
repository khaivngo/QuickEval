<?php
/**
 * Gets information about whether or not show original.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


try {
  //gets whether the experiment is to show original
  $stmt = $db->prepare("Select showOriginal FROM experiment WHERE id = :id");

  $stmt->execute(array(':id' => $_POST['experimentId']));
	$res = $stmt->fetchAll();
	
	echo json_encode($res);
	exit;
} catch (Exception $ex) {
  // ChromePhp::log($excpt->getMessage());
}

?>