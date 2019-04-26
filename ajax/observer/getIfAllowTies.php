<?php
/**
 * Will get information for a given experiment whether or not it allows ties.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
  //gets whether the experiment is to allow ties
  $stmt = $db->prepare("SELECT allowTies from experiment WHERE id = :id");

  $stmt->execute(array(':id' => $_POST['experimentId']));
	$res = $stmt->fetchAll();
	
	echo json_encode($res);	
	exit;
} catch (Exception $excpt) {
  // ChromePhp::log($excpt->getMessage());
}

?>