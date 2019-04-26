<?php
/**
* Get all categories from categoryname belonging to the logged in user.
*/

require_once('../../db.php');

if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}

if ($_SESSION['user']['userType'] > 2) {
	exit;
}

try {
	$userId = $_SESSION['user']['id'];

	$sql = "SELECT id, name FROM categoryname WHERE personId = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$userId);
	$sth->execute();

	$result = $sth->fetchAll();
	echo json_encode($result);
	exit;
} catch(PDOException $excpt) {
	echo json_encode("0");
	exit;
}

?>
