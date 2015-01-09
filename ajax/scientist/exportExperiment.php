<?php
/**
* This file contains all functions for exporting an experiment.
*/

require_once('../../db.php');
if (!isset($_SESSION['user'])) {
               header("Location: ../../login.php"); 
            }
if($_SESSION['user']['userType'] > 2) {
	return;
}			
            
	$option = $_GET['option'];
	
	if($option == "generateSQL") {
		deleteOlderFiles();
		$experimentId = $_GET['experimentId'];
		//////////////////////////////////////////////////////////////////////
		// Experiment
		//////////////////////////////////////////////////////////////////////
		$sql = "SELECT * FROM experiment WHERE id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetch();
		serializeData($result, "experiment.txt");
		if($result['experimentType'] == 1)	 {			//Rating
				
		} else if ($result['experimentType'] == 3) {	//Category
			$sql = "SELECT categoryname.id,categoryname.name,categoryname.personId,
			        categoryname.standardFlag FROM categoryname 
					JOIN experimentcategory ON categoryname.id = experimentcategory.category
					WHERE experimentcategory.experiment = ?;";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$experimentId);
			$sth->execute();
			$result = $sth->fetchAll();
			serializeData($result,"categoryname.txt");
		}
		//////////////////////////////////////////////////////////////////////
		// PictureQueue
		//////////////////////////////////////////////////////////////////////
		$sql = "SELECT picturequeue.id, picturequeue.title FROM picturequeue
				JOIN experimentOrder ON experimentOrder.pictureQueue = pictureQueue.id
				JOIN experimentqueue ON experimentqueue.id = experimentorder.experimentqueue
				JOIN experiment ON experiment.id = experimentqueue.experiment
				WHERE experiment.id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		serializeData($result,"pictureQueue.txt");
		
		//PictureOrder
		$sql = "SELECT pictureorder.id,pictureorder.pOrder, pictureorder.picture,pictureorder.picturequeue FROM pictureOrder
				JOIN picturequeue ON picturequeue.id = pictureOrder.pictureQueue
				JOIN experimentOrder ON experimentOrder.pictureQueue = pictureQueue.id
				JOIN experimentqueue ON experimentqueue.id = experimentorder.experimentqueue
				JOIN experiment ON experiment.id = experimentqueue.experiment
				WHERE experiment.id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		serializeData($result,"pictureorder.txt");
		//////////////////////////////////////////////////////////////////////
		// experimentOrder SORTER PÅ eORDER
		//////////////////////////////////////////////////////////////////////
		$sqlExport['experimentorder'] = array();
		$sql = 
		"SELECT eOrder,pictureSet,experimentQueue,pictureQueue,instruction FROM experimentqueue 
		INNER JOIN experimentorder ON experimentqueue.id=experimentorder.experimentqueue
		WHERE experimentqueue.experiment = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		serializeData($result,"experimentorder.txt");
		//////////////////////////////////////////////////////////////////////
		// instructions
		//////////////////////////////////////////////////////////////////////
		$sql = "SELECT instruction.id, instruction.standardFlag,instruction.text,instruction.personId FROM instruction
				JOIN experimentorder ON experimentorder.instruction = instruction.id
				JOIN experimentqueue ON experimentqueue.id = experimentorder.experimentqueue
				JOIN experiment ON experiment.id = experimentqueue.experiment
				WHERE experiment.id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		serializeData($result,"instruction.txt");
		//////////////////////////////////////////////////////////////////////
		// infotype
		//////////////////////////////////////////////////////////////////////
		$sql = "SELECT infotype.id, standardFlag,info, person FROM infotype
				JOIN experimentinfotype ON experimentinfotype.infotype = infotype.id
				WHERE experimentinfotype.experiment = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		serializeData($result, "infotype.txt");		
		//////////////////////////////////////////////////////////////////////
		// Pictures
		//////////////////////////////////////////////////////////////////////
		
		$pictures = getAllPicturesInExperiment($experimentId, $db);
		$picturesUrl = array();
		$pictureSQL = array();
		$pictureSet = array();
		$pictureSetFound = array();
		foreach($pictures as $picture) {
			$picturesUrl[] = getPictureUrl($picture, $db);
			$sql = "SELECT * FROM picture WHERE id = ?;";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$picture);
			$sth->execute();
			$result = $sth->fetch();
			$pictureSQL[] = $result;
			
			if(!in_array($result['pictureSet'], $pictureSetFound)) {
				//Gets pictureSet for a picture, and stores it in array.
				$pictureSetFound[] = $result['pictureSet'];
				$sql = "SELECT * FROM pictureset WHERE id = ?;";
				$sth = $db->prepare($sql);
				$sth->bindParam(1,$result['pictureSet']);
				$sth->execute();
				$result = $sth->fetch();
				$pictureSet[] = $result;
				

			}
		}
		foreach($picturesUrl as $picture) {
			createZipfileWithPictures($experimentId, $picture);
		}

		serializeData($pictureSQL, "picture.txt");
		serializeDatA($pictureSet, "pictureset.txt");
		
		$filePath = "data/" . $_SESSION['user']['id'] . "/" . $experimentId . ".zip";
		if(file_exists($filePath)) {
			echo json_encode($filePath);
		} else {
			json_encode(0);
		}
	}
/**
 * Will write data to a given file and add it into the zipped folder.
 * @param $data is an array with JSON data.
 * @param $filename of the file we want to add to the zipfile.
 * 
 */
function serializeData($data, $filename) {
	$dir = "data/" . $_SESSION['user']['id'] . "";
	
	if(!is_dir("data")) {		//This should only run ONCE in case the /data/ folder doesnt exist.
		mkdir("data", true);
	}
	if(!is_dir($dir)) {
			mkdir($dir,true);
	}
	$writeableData = json_encode($data);
	//file_put_contents(($dir . "/" .$filename), $writeableData);
	
	$zip = new Ziparchive();
	$fileName = "data/" . $_SESSION['user']['id'] ."/". $_GET['experimentId'] . ".zip";
	$open = $zip->open($fileName, ZIPARCHIVE::CREATE);
	if($open !== true) {
		die ("Troubles, please try again!");
	}
	$zip->addFromString($filename, $writeableData);
	$zip->close(); 
}

/**
 * Will generate URL for a given picutre
 * @param $picture an array with pictureinformation.
 * @return $url string with url to the picture.
 */
function generateUrl($picture){
	$index = strripos($picture[1],".");
	$fileType = substr($picture[1],$index, strlen($picture[1]));
	$url = array();
	$url['uploadFolder'] = "uploads/" . $picture['person'] . "/" . $picture[4] . "/";		
	$url['url'] = "../../" . $url['uploadFolder'] . $picture['url'] . $fileType; 	

	$url['id'] = $picture[0];
	$url['name'] = $picture[1];
	return $url;
}

/**
 * will get url for a given pictureID.
 * @param $pictureId for a given picture from the database.
 * @param $db PDO connection to the database.
 * @return URL for given pictureID.
 */
function getPictureUrl($pictureId, $db) {
	$sql = "SELECT * FROM picture
			JOIN pictureset ON picture.pictureSet = pictureset.id
			WHERE picture.id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$pictureId);
	$sth->execute();
	$row = $sth->fetch();
	$url = generateUrl($row);
	return $url;
}
/**
 * Will get all individual pictures for a given experiment
 * @param $experimentId int with experimentID
 * @param $db PDO connection to the database.
 * @return Array containing ID for all pictures in the given experiment.
 */
	function getAllPicturesInExperiment($experimentId, $db) {
		$experimentId = $_GET['experimentId'];

		$sql = "SELECT picture.id, picture.pictureset FROM picture
			JOIN pictureorder ON picture.id = pictureorder.picture
			JOIN picturequeue ON pictureorder.picturequeue = picturequeue.id
			JOIN experimentorder ON picturequeue.id = experimentorder.picturequeue
			JOIN experimentqueue ON experimentorder.experimentqueue = experimentqueue.id
			JOIN experiment ON experimentqueue.experiment = experiment.id
			WHERE experiment.id = ?
			GROUP BY picture.id;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		
		
		$pictureArray = array();
		$foundPictureSets = array();
		foreach($result as $pictureId) {
			if(!in_array($pictureId['pictureset'],$foundPictureSets)) {
				$foundPictureSets[] = $pictureId['pictureset'];
				$sql = "SELECT picture.id FROM picture WHERE pictureset = :pictureset AND isOriginal = 1;";
				$sth = $db->prepare($sql);
				$sth->bindParam(':pictureset', $pictureId['pictureset']);
				$sth->execute();
				$result = $sth->fetch();
				$pictureArray[] = $result['id'];
			}
			$pictureArray[] = $pictureId['id'];
		}
		return $pictureArray;
	}
	/**
	 * Will create zip folder with pictures for export.
	 * @param $experimentId which is to be name of the folder.
	 * @param $picture has information about picture to add to the database.
	 */
	function createZipfileWithPictures($experimentId, $picture) {
		$zip = new Ziparchive();
		$fileName = "data/" . $_SESSION['user']['id'] ."/". $_GET['experimentId'] . ".zip";
		if ($zip->open($fileName, ZipArchive::CREATE)!==TRUE) {
			return;
		}
		$zip->addFile($picture['url'], ($picture['uploadFolder'] . $picture['name']));
		$zip->close();
	}
	/**
	 * Function created in order to clean up old zip files on the servers.
	 * This is a bad solution, but we dont have access to the server in order to run CRON jobs, and therefore this is the best solution.
	 */
	function deleteOlderFiles() {
		error_reporting(0);
		$hours = 43200;	//12 hours.
		$directory = new RecursiveDirectoryIterator("data/");
		$iterator = new RecursiveIteratorIterator($directory);
		$files = array();
		foreach ($iterator as $info) {
		   if(is_file($info) == true) {
		   	$age = time() - filemtime($info);
		   	if($age > $hours) {
		   		unlink($info);
		   	}
		  } 
		}
		
		$directories = scandir("data/");
		foreach($directories as $dir) {
			if($dir != "." && $dir != "..") {
				if(rmdir("data/" . $dir));
			}
		}
}
?>