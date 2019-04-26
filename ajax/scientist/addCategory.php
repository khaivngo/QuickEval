<?php
/**
* This file will add a new category to the general categories table,
* as well as link it to a experiment.
*/

require_once('../../db.php');

if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}

try {
	$db->beginTransaction();
	$categoryText = $_GET['categoryText'];
	$experimentId = $_GET['experimentId'];
	$userId = $_SESSION['user']['id'];
	
	$sql = "INSERT INTO `categoryname` (`id`, `name`, `personId`, `standardFlag`) VALUES (NULL, ?, ?, NULL);";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$categoryText);
	$sth->bindParam(2,$userId);
	$sth->execute();
	
	$categoryNameId = $db->lastInsertId();
	$sql = "INSERT INTO `experimentcategory` (`id`, `category`, `experiment`) VALUES (NULL, ?, ?);";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$categoryNameId);
	$sth->bindParam(2,$experimentId);
	$sth->execute();
	$db->commit();
	
	echo json_encode("1");
	exit;
} catch(PDOException $excpt) {
	$db->rollBack();
	echo json_encode("0");
	exit;
}

?>