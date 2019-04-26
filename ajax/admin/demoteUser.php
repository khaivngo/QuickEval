<?php
/**
 * Will upgrade a users access level based on email.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$email = $_POST['email'];
	
try	{
  //updates user's access level for the owner of email		
	$stmt = $db->exec("UPDATE experiment LEFT JOIN person ON person.email='" . $email . "' AND person.id=experiment.person SET isPublic = '0'");
	echo json_encode($stmt);
  exit;
} catch (Exception $ex) {
  //ChromePhp::log($ex->getMessage());
}

?>