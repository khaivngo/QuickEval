<?php
	require_once('../../db.php');
	require_once('../../ChromePhp.php');

	$userId = $_SESSION['user']['id'];             //fetching user id from session

	try {                                   //updates email for the particular user
		$stmt = $db->prepare("UPDATE person SET password =:password WHERE id = '" . $userId . "'");

		$stmt->execute(array(':password' => $_POST['password']));

	} catch (Exception $ex) {
		ChromePhp::log($ex->getMessage());
	}



?>