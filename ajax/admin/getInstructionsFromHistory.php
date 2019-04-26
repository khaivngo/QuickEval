<?php

/**
 * This file is used for getting instruction either by scientist or system,
 * it also used to fetch which instructions are associated with experiments.
 */

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

$option = $_POST['option'];  //option might be 0 = access within admin panel or 1 = access within scientist panel
$mode = $_POST['mode'];      //mode might be 0 = access within admin panel or 1 = access within scientist panel


//If access level is superuser all instructions in DB are fetched.
if ($res[0][0] == "Superuser" && $mode == 0 && $option != 3) {				//fetches all instructions
	try {
		$stmt = $db->prepare("SELECT text, id FROM instruction WHERE text != 'NULL'");
	
		$stmt->execute();
		$res = $stmt->fetchAll();
		echo json_encode($res);
        exit;
	} catch (PDOException $excpt) {}
}
else if ($res[0][0] == "Admin" && $mode == 0 && $option != 3)	{		//Fetches all instructions from DB that belongs to user associated with institution of admin
	$workPlace = $res2[0][0];
	
	try {
		$stmt = $db->prepare("SELECT DISTINCT instruction.text, instruction.id FROM instruction, person JOIN workplacebelongs ON person.id = workplacebelongs.personId WHERE workplacebelongs.workPlace = '" . $workPlace . "' AND text != 'NULL' ");
	
		$stmt->execute();
		$res = $stmt->fetchAll();
		echo json_encode($res);
    
	} catch (PDOException $excpt) {}
}
else if ($res[0][0] == "Scientist" || $option == 1 && $mode == 1)	{ //Fetches all instructions belonging to current scientist.
	$workPlace = $res2[0][0];
	
	try {
		$stmt = $db->prepare("SELECT instruction.text, instruction.id FROM person JOIN instruction ON person.id = instruction.personId WHERE instruction.personId = '" . $userId . "' AND text != 'NULL' ");
	
		$stmt->execute();	
		$res = $stmt->fetchAll();
		echo json_encode($res);
    exit;
	} catch (PDOException $excpt) {}
}
else if ($option == 3 && $mode == 1) {     //Fetches instruction(s) for a particular scientist that is associated with an exisiting experiment
    try {
        $stmt = $db->prepare(
            "SELECT DISTINCT experiment.title, experiment.id, experimentorder.instruction, instruction.text, instruction.id FROM experiment as origin, experimentqueue " .
            " JOIN experiment ON experiment.id = experimentqueue.experiment " .
            " JOIN experimentorder ON experimentqueue.id = experimentorder.experimentqueue " .
            " JOIN instruction ON instruction.id = experimentorder.instruction " .
            "  WHERE experiment.person = '" . $userId . "'"
        );

        $stmt->execute();
        $res = $stmt->fetchAll();
        echo json_encode($res);
        exit;
    } catch (PDOException $excpt) {}
}
else if ($option == 3 && $mode == 0) {    //Fetches any instruction(s) that are associated with any experiment.
    try {
        $stmt = $db->prepare(
          "SELECT DISTINCT experiment.title, experiment.id, experimentorder.instruction, instruction.text, instruction.id FROM experiment as origin, experimentqueue ".
          " JOIN experiment ON experiment.id = experimentqueue.experiment " .
          " JOIN experimentorder ON experimentqueue.id = experimentorder.experimentqueue " .
          " JOIN instruction ON instruction.id = experimentorder.instruction"
        );

        $stmt->execute();
        $res = $stmt->fetchAll();
        echo json_encode($res);
        exit;
    } catch (PDOException $excpt) {}
}

?>
