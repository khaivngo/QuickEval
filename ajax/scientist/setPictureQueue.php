<?php
/**
 * This file will handle all the different pictureOrders for all experiments.
 * 
 */
require_once('../../db.php');
include_once('../../functions.php');
if (!isset($_SESSION['user'])) {
	header("Location: ../../login.php"); 
}
if($_SESSION['user']['userType'] > 2) {
	return;
}
/**
 * A function that will create a pictureQueue.
 * @param $images an awway with imageId's of which to create a queue from.
 * @param $imagesShownRightAndLeft 1/0 of whether to show a picture on both sides.
 * @return $pairs, an array with the new queue.
 */
function makeQueue($images, $imagesShownRightAndLeft) {
	$pairs = array();	
	$index = 1;
	$arrIndex = 0;
	foreach($images as $image) {
		for($i = $index;$i < count($images);$i++ ) {
			$pairs[$arrIndex][0] = $image['id'];
			$pairs[$arrIndex][1] = $images[$i]['id'];
			if($imagesShownRightAndLeft == 1) {
				$arrIndex++;
				$pairs[$arrIndex][0] = $images[$i]['id'];
				$pairs[$arrIndex][1] = $image['id'];
			}
			$arrIndex++;
		}
		$index++;
	}
	shuffle($pairs);	
	return $pairs;
}

$option = $_GET['option'];

if($option == "generateRandom") {
$rightAndLeft = $_GET['rightAndLeft'];	//In case the scientist chooses to view pictures both right and left.
	$imagesetId = $_GET['imagesetId'];
	
	$sql = "SELECT * FROM picture WHERE pictureSet = ? AND isOriginal = 0";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $imagesetId);
	$sth->execute();
	$images = $sth->fetchAll();
	
	$imagesToCheck = array();			//CheckOwnerForPicture expects simply id, not a whole class
	foreach($images as $image) {
		$imagesToCheck[] = $image['id'];
	}
	
	$pairs = makeQueue($images, $rightAndLeft);
	
	$db->beginTransaction();

	$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, NULL);";
	$sth = $db->prepare($sql);
	$sth->execute();
	$pictureQueueId =  $db->lastInsertId();
	$order = 0;
	foreach($pairs as $pair) {
		$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$order);
		$sth->bindParam(2,$pair[0]);
		$sth->bindParam(3,$pictureQueueId);
		$sth->execute();
		
		$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$order);
		$sth->bindParam(2,$pair[1]);
		$sth->bindParam(3,$pictureQueueId);
		$sth->execute();	
		
		$order++;
	}
	$db->commit();
	echo json_encode($pictureQueueId);
}

else if($option == "notRandom") {
/**
 * Får medsendt en array med to bildeideer.  Disse skal inn i en kanskje eksisterendes pictureQueue OM DEN FINNES
 * Har ikke addet sikkerhetssjekker her.
 * Kan vell egentlig gå ut i fra at denne funksjonen blir kjørt en gang for hvert enkelt bildesett som lages. typ ganske mange ganger
 */
 $rightAndLeft = $_GET['rightAndLeft'];	//In case the scientist chooses to view pictures both right and left.
$images = $_GET['images'];
	$pictureQueueId = $_GET['pictureQueueId'];	//This is 0 if it is for a new set.
	$imagesArray = array();
	
	
	foreach($images as $image) {		
		$sql = "SELECT * FROM picture WHERE id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$image);
		$sth->execute();
		$imagesArray[] = $sth->fetch();
		
	}
	
	$pairs = makeQueue($imagesArray, $rightAndLeft);
	if(count($pairs > 1)) {
		$db->beginTransaction();
		$order;
		if($pictureQueueId == 0) {	//Generate new pictureQueue
			$name = $_GET['name'];
			$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$name);
			$sth->execute();
			
			$pictureQueueId =  $db->lastInsertId();	
			$order = 0;
			
		} else {					//PictureQueue exists.  Need to get order from pictureOrder
			$sql = "SELECT * FROM pictureorder where pictureQueue = ?
			ORDER BY pOrder DESC
			LIMIT 1";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$pictureQueueId);
			$sth->execute();
			$result = $sth->fetch();
			$order = ($result['pOrder']+1);
		}
		
		foreach($pairs as $pair) {
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[0]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();
			
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$order);
			$sth->bindParam(2,$pair[1]);
			$sth->bindParam(3,$pictureQueueId);
			$sth->execute();
			$order++;
		}
		echo json_encode($pictureQueueId);
	}
	$db->commit();
}

else if($option == "ratingCategory") {
	$db->beginTransaction();
	try {
		//Trenger bare å lagre med eOrder 0
		$imagesetId = $_GET['imagesetId'];
		
		$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, NULL);";
		$sth = $db->prepare($sql);
		$sth->execute();		
		$pictureQueueId =  $db->lastInsertId();
		
		$sql = "SELECT * FROM picture WHERE pictureSet = ? AND isOriginal = 0;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1, $imagesetId);
		$sth->execute();
		$result = $sth->fetchAll();
		
		foreach($result as $image) {
			$sql = "INSERT INTO `pictureorder` (`pOrder`, `picture`, `pictureQueue`) VALUES (0, ?, ?);";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$image['id']);
			$sth->bindParam(2,$pictureQueueId);
			$sth->execute();
		}	
		$db->commit();
		echo json_encode($pictureQueueId);
	} catch(Exception $e) {
	$db->rollBack();
	echo json_encode(0);
	}

}



?>