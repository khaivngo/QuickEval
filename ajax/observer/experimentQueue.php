<?php
/**
 * This file controls everything with retrieving pictures and instructions when doing an experiment.
 */
require_once('../../db.php');
$option = $_GET['option'];

if($option == "startNewObserverExperiment") {
	$experimentId = $_GET['experimentId'];
	$_SESSION['user']['activeObserverExperiment'] = $experimentId;
	
	$sql = "SELECT * FROM experimentqueue 
			JOIN experimentorder ON experimentorder.experimentqueue=experimentqueue.id
			WHERE experimentqueue.experiment = ?
			ORDER BY experimentorder.eOrder ASC;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$experimentId);
	$sth->execute();
	$result = $sth->fetchAll();
	$_SESSION['activeObserverExperiment']['index'] = 0;
	$_SESSION['activeObserverExperiment']['experimentorder'] = $result;
	$_SESSION['activeObserverExperiment']['pictureOrder'] = null;
}

/**
* This will get the next position, 
* whether it be a pictureOrder, new pictureQueue, or instruction
*/


else if($option == "getNextPosition") {
	//echo "GetNextPosition</br>";
	if($_SESSION['activeObserverExperiment'] == null) {
		echo json_encode(array("type" => "finished"));
		return;	
	}
	//PictureQueue er aktiv, sjekk om det finnes flere bilder i pictureQueuen.
	//
	$exType = getExperimentType($db);
	if($_SESSION['activeObserverExperiment']['pictureOrder'] != null && ($exType == "pair" || $exType == "category")) {
		//echo "</br>pictureOrder != null";
		$index = $_SESSION['activeObserverExperiment']['activePictureOrder'][count($_SESSION['activeObserverExperiment']['activePictureOrder'])-1];
		//echo "</br> Index = {$index}";
		if($index < count($_SESSION['activeObserverExperiment']['pictureOrder'])-1) {
			//echo "</br>Forsøker å hente ut fra bildekø";
			if($exType == "pair") {
				$_SESSION['activeObserverExperiment']['activePictureOrder'][0]+=2;				//This is for pair comparison only.
				$_SESSION['activeObserverExperiment']['activePictureOrder'][1]+=2;
				writeOutPicture();	
			}
			else if($exType == "category") {
				$_SESSION['activeObserverExperiment']['activePictureOrder'][0]+=1;				//This is for pair comparison only.
				//echo "</br>Prover å hente ut pictureOrder = " . $_SESSION['activeObserverExperiment']['activePictureOrder'][0];
				writeOutPictureForCategory();
				
			}
			
		}
			else {	//ingen flere bilder igjen
				//echo "</br>Ingen Flere bilder igjen";
				$_SESSION['activeObserverExperiment']['activePictureOrder'] = null;
				$_SESSION['activeObserverExperiment']['pictureOrder'] = null;
				finishedCurrentExperimentOrder($db);
			} 
	}
	else {
		//echo "</br>finishedExperimentOrder";
		finishedCurrentExperimentOrder($db);
	}	
	//pictureQueue er ikke aktiv.  Da kan det hentes ut instruction eller pictureQuue

}
/**
 * This function is run every time an experimentOrder is finished. 
 * Will write out picture, or instruction depending on what current experimentOrder is.
 * @param $db = PDO connection to the database.
 */
function finishedCurrentExperimentOrder($db) {
	$exType = getExperimentType($db);
	if($_SESSION['activeObserverExperiment']['index'] <= (count($_SESSION['activeObserverExperiment']['experimentorder'])-1)) {
		//echo "</br> Still more experimentOrders left";
		$index = $_SESSION['activeObserverExperiment']['index'];
		$_SESSION['activeObserverExperiment']['activeExperimentOrder'] = $_SESSION['activeObserverExperiment']['experimentorder'][$index];
		$_SESSION['activeObserverExperiment']['index']++;
		
	//	var_dump($_SESSION['activeObserverExperiment']);	
		if($_SESSION['activeObserverExperiment']['activeExperimentOrder']['pictureQueue'] != null) {	//The current experimentOrder has a foreign key
			//echo "</br>pictureQueue is active";
			newPictureQueue($db);
			//echo "Har akkurat laget newPictureQueue, exType er = $exType";
			if($exType == "pair") {																		//which points to a pictureQueue
				writeOutPicture();
			}
			else if($exType == "category") {
				//echo "</br>Kjorer category";
				writeOutPictureForCategory();
			}
			else if($exType == "rating") {
				//echo "</br>Kjorer rating";
				writeOutPictureForRating();
			}
		}
		else if($_SESSION['activeObserverExperiment']['activeExperimentOrder']['instruction'] != null) { //The current experimentOrder has a foreign key
			writeExperimentInstruction($db);															 //which points to a instruction
		}
	}
	//Experiment is finished
	else {
	//	echo "ExperimentFinished";
		$_SESSION['activeObserverExperiment'] = null; 
		echo json_encode(array("type" => "finished"));	
	}
}


/**
 * Returns an instruction from the database.
 */
function writeExperimentInstruction($db) {
		$sql = "SELECT * FROM experimentorder
				JOIN instruction ON experimentorder.instruction=instruction.id
				WHERE eOrder = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$_SESSION['activeObserverExperiment']['activeExperimentOrder']['eOrder']);
		$sth->execute();
		$result = $sth->fetch();
		echo json_encode(array("type" => "experimentinstruction", "experimentinstruction" => $result['text']));
}

/**
 * This function is run every time you start a new pictureQueue for an experiment
 * Will set up correct order for pictures, and store it in session.
 * @param $db = PDO connection to the database.
 */
function newPictureQueue($db) {
				//Ny oppstart av bildekø.
				//Henter ut alle bilder for en bildekø.
	$exType = getExperimentType($db);
	//echo "<br/>New pictureQueue";
	//Må muligens adde ORDER BY pOrder her ? 
	//This SQL gets ALL the pictures for a given pictureQueue.
	$sql = "SELECT * FROM experimentorder
						JOIN pictureorder ON pictureorder.pictureQueue=experimentorder.pictureQueue
						WHERE experimentorder.eOrder = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$_SESSION['activeObserverExperiment']['activeExperimentOrder']['eOrder']);
	$sth->execute();
	$result = $sth->fetchAll();

      //  if($_SESSION['activeObserverExperiment']['sortingalgorithm'] == 1) {
            shuffle($result);
       // }
	$_SESSION['activeObserverExperiment']['pictureOrder'] = $result;                
        
        
	if($exType == "pair") {		//Pair comparison will always return two pictures.  "0" and "1" are indexes in the stored array.
	//	echo "<br/>PairComparisjoN";
			$_SESSION['activeObserverExperiment']['activePictureOrder'] = array(0 => 0, 1 => 1);
	}
	else if($exType == "category") {	//Category will always return ALL pictures, or just one.
			//echo "<br/>Category";
			$_SESSION['activeObserverExperiment']['activePictureOrder'] = array(0 => 0);
	}
}
/**
 * Will write out all pictures in a experimetnOrder for a rating experiment.
 */
function writeOutPictureForRating() {
	$pictures = array();
	$index = 0;
	foreach($_SESSION['activeObserverExperiment']['pictureOrder'] as $pictureOrder) {
		$pictures[$index]['pictureId'] = $pictureOrder['picture'];	//Gets all the pictureId's from given pictureOrder
		$pictures[$index]['pictureOrderId'] = $pictureOrder['id'];		//id for pictureOrder
		$index++;
	}
	echo json_encode(array("type" => "pictureQueue","pictureQueue" => $pictures));
}
/**
 * Will write out all pictures in a experimetnOrder for a category experiment.
 */
function writeOutPictureForCategory() {
				$pictures = array();
			    $picture1 = $_SESSION['activeObserverExperiment']['activePictureOrder'][0];	//Current index
				$pictures[0]['pictureId'] =  $_SESSION['activeObserverExperiment']['pictureOrder'][$picture1]['picture'];	//Gets ID from database for picture
				$pictures[0]['pictureOrderId'] = $_SESSION['activeObserverExperiment']['pictureOrder'][$picture1]['id'];		//id for pictureOrder
				echo json_encode(array("type" => "pictureQueue","pictureQueue" => $pictures));
}
/**
 * Will write out all pictures in a experimetnOrder for a pairing experiment.
 */
function writeOutPicture() {
				$picture1 = $_SESSION['activeObserverExperiment']['activePictureOrder'][0];
				$picture2 = $_SESSION['activeObserverExperiment']['activePictureOrder'][1];
				$pictures[0]['pictureId'] =  $_SESSION['activeObserverExperiment']['pictureOrder'][$picture1]['picture'];	//Gets ID from database for picture
				$pictures[0]['pictureOrderId'] = $_SESSION['activeObserverExperiment']['pictureOrder'][$picture1]['id'];
				
				$pictures[1]['pictureId'] =  $_SESSION['activeObserverExperiment']['pictureOrder'][$picture2]['picture'];	//Gets ID from database for picture
				$pictures[1]['pictureOrderId'] = $_SESSION['activeObserverExperiment']['pictureOrder'][$picture2]['id'];
				echo json_encode(array("type" => "pictureQueue","pictureQueue" => $pictures));
}
/**
 * Gets experimentType for a active experiment.
 * @return The type of the active experiment.
 */
function getExperimentType($db) {
	$sql = "SELECT type FROM experimenttype
			JOIN experiment 
			ON experimenttype.id = experiment.experimentType
			WHERE experiment.id = ?";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$_SESSION['activeObserverExperiment']['experimentorder'][0]['experiment']);
	$sth->execute();
	$exType = $sth->fetch();
	return $exType['type'];
}
?>