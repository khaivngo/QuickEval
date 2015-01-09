<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');
	ChromePhp::log();
$email = $_POST['email'];
	
try	{

	
	
                                  //updates user's access level for the owner of email		
	$stmt = $db->exec("UPDATE experiment LEFT JOIN person ON person.email='" . $email . "' AND person.id=experiment.person SET isPublic = '0'");
	echo json_encode($stmt);

    } catch (Exception $ex) {
        ChromePhp::log($ex->getMessage());
    }


	
	
	//echo json_encode($stmt);
	
	

?>