<?php

    /**
    * This file will add a new category to the database.
    */
    require_once('../../db.php');

    if (!isset($_SESSION['user'])) {
       header("Location: ../../login.php");
       exit;
    }

	try {

	$categoryNameId = $_GET['categoryNameId'];
	$experimentId = $_GET['experimentId'];

	$sql = "INSERT INTO `experimentcategory` (`category`, `experiment`) VALUES (?, ?);";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$categoryNameId);
	$sth->bindParam(2,$experimentId);
	$sth->execute();

	echo json_encode(1);
	} catch(PDOException $excpt) {
		echo json_encode(0);
	}


?>
