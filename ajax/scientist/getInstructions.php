<?php
/**
 * Will get ALL instructions for a given person.
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

$userId = $_SESSION['user']['id'];

$sql = "SELECT * FROM instruction WHERE personId = ?;";
$sth = $db->prepare($sql);
$sth->bindParam(1,$userId);
$sth->execute();
$result = $sth->fetchAll();
echo json_encode($result);
exit;


?>
