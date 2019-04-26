<?php
/**
 * Will get ALL infotypes for a given person.
 */
require_once('../../db.php');
include_once('../../functions.php');

if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}

if ($_SESSION['user']['userType'] > 2) {
	exit;
}

$option = $_GET['option'];
$userId = $_SESSION['user']['id'];

if($option == "getInputfields") {
	$sql = "SELECT * FROM infotype WHERE person = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$userId);
	$sth->execute();
	$result = $sth->fetchAll();
	echo json_encode($result);
  exit;
}


?>
