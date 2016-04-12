<?php
/**
* This file will add a new category to the database.
*/

require_once('../../db.php');
if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}
if($_SESSION['user']['userType'] > 2) {
	return;
}

		try {
		$userId = $_SESSION['user']['id'];

		$sql = "SELECT id, name FROM categoryname WHERE personId = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$userId);
		$sth->execute();

		$result = $sth->fetchAll();
		echo json_encode($result);
		} catch(PDOException $excpt) {
			echo json_encode("0");
		}


?>
