<?php
	require_once('../../db.php');
	require_once('../../ChromePhp.php');

	$userId = $_SESSION['user']['id'];             //fetching user id from session

	try {                                   //updates chosen background colour for the particular user
	
		ChromePhp::log("Inside try sql");          //for logging purposes
		
		$stmt = $db->prepare("UPDATE person SET background=:backgroundColour WHERE id = '" . $userId . "'");
		
		$stmt->execute(array(':backgroundColour'	=> $_POST['backgroundColour']));

		ChromePhp::log("background colour updated");          //for logging purposes
	} catch (Exception $ex) {
		ChromePhp::log($ex->getMessage());
	}

?>