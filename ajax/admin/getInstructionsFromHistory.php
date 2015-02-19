<?php

/*Fetches users based on whether you are a superuser or admin*/
 
require_once('../../db.php');
require_once('../../ChromePhp.php');

$accessLevel = $_SESSION['user']['email'];
$userId = $_SESSION['user']['id'];
										//fetches users access level
$stmt = $db->prepare("SELECT title FROM userType as a WHERE EXISTS (SELECT * FROM person as b WHERE b.email = :email AND b.userType = a.id)");
$stmt->execute(array(':email' => $accessLevel));	
$res = $stmt->fetchAll();
									//fetches users workplace
$stmt = $db->prepare("SELECT workPlace FROM workplacebelongs WHERE personId = :id");
$stmt->execute(array(':id' => $userId));	
$res2 = $stmt->fetch();


//If access level is superuser all instructions in DB are fetched.

if($res[0][0] == "Superuser")	{				//fetches all users
	try {
		$stmt = $db->prepare("SELECT text FROM instruction WHERE text != 'NULL'");
	
		$stmt->execute();
	
		$res = $stmt->fetchAll();

		echo json_encode($res);
    
	} catch (PDOException $excpt) {
		ChromePhp::log($excpt->getMessage());
	}
}
else if ($res[0][0]) == "Admin"	{		//Fetches all instructions from DB that belongs to user associated with institution of admin
	$workPlace = $res2[0][0];
	
	try {
		$stmt = $db->prepare("Select instruction.text from person JOIN workplacebelongs ON person.id = workplacebelongs.personId WHERE workplacebelongs.workPlace = '" . $workPlace . "' AND text != 'NULL' ");
	
		$stmt->execute();	
	
		$res = $stmt->fetchAll();

		echo json_encode($res);
    
	} catch (PDOException $excpt) {
		ChromePhp::log($excpt->getMessage());
	}
	
}
else if ($res[0][0]) == "Scientist"	{ //Fetches all instructions belonging to current scientist.
	$workPlace = $res2[0][0];
	
	try {
		$stmt = $db->prepare("Select instruction.text from person JOIN instruction ON person.id = instruction.personId WHERE instruction.personId = '" . $userId . "' AND text != 'NULL' ");
	
		$stmt->execute();	
	
		$res = $stmt->fetchAll();

		echo json_encode($res);
    
	} catch (PDOException $excpt) {
		ChromePhp::log($excpt->getMessage());
	}

}

?>