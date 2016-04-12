<?php
/**
* This file will import a complete experiment to the database using a uploaded zipfile.
*/

require_once('../../db.php');
if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

if($_SESSION['user']['userType'] > 2) {
	return;
}

	$option = $_GET['option'];
	if($option == "generateExperiment") {
   	try {
		$zip = new ZipArchive;
		$fileName = "randomName";
		$targetDir = "../../uploads/zipfiles/" . $_SESSION['user']['id'];
		$fileName = "randomName";
		$res = $zip->open($targetDir . "/". $fileName . ".zip", ZIPARCHIVE::CREATE);
		if($res === TRUE) {
			if (!file_exists($targetDir . "/" . $fileName)) {
				@mkdir($targetDir . "/" . $fileName ,0777, true);
			}

			$zip->extractTo($targetDir . "/" . $fileName);
			$zip->close();
		} else {
			return;
		}

		$data = file_get_contents($targetDir . "/".$fileName. "/experiment.txt");
		$experiment = json_decode($data, true);
		unlink($targetDir . "/".$fileName. "/experiment.txt");

		$data = file_get_contents($targetDir."/".$fileName. "/picture.txt");
		$picture = json_decode($data, true);
		unlink($targetDir."/".$fileName. "/picture.txt");

		$data = file_get_contents($targetDir."/".$fileName. "/experimentorder.txt");
		$experimentorder = json_decode($data, true);
		unlink($targetDir."/".$fileName. "/experimentorder.txt");

		$data = file_get_contents($targetDir."/".$fileName. "/infotype.txt");
		$infotype = json_decode($data, true);
		unlink($targetDir."/".$fileName. "/infotype.txt");

		$data = file_get_contents($targetDir."/".$fileName. "/instruction.txt");
		$instruction = json_decode($data, true);
		unlink($targetDir."/".$fileName. "/instruction.txt");

		$data = file_get_contents($targetDir."/". $fileName."/pictureorder.txt");
		$pictureorder = json_decode($data, true);
		unlink($targetDir."/". $fileName."/pictureorder.txt");

		$data = file_get_contents($targetDir."/".$fileName. "/pictureQueue.txt");
		$picturequeue = json_decode($data, true);
		unlink($targetDir."/".$fileName. "/pictureQueue.txt");

		$data = file_get_contents($targetDir."/".$fileName. "/pictureset.txt");
		$pitureset = json_decode($data, true);
		unlink($targetDir."/".$fileName. "/pictureset.txt");

		$db->beginTransaction();
		$sql = "INSERT INTO `experiment`
		(`id`, `title`, `shortDescription`, `longDescription`,
		 `date`, `isPublic`, `allowColourBlind`, `backgroundColour`,
		 `allowTies`, `showOriginal`, `samePair`, `horizontalFlip`,
		 `monitorDistance`, `lightType`, `naturalLighting`,
		 `screenLuminance`, `whitePoint`, `whitePointRoom`,
		 `ambientIllumination`, `person`, `experimentType`,
		 `timer`, `inviteHash`)

		 VALUES (
		 NULL, :title, :shortdesc, :longdesc,
		 CURRENT_TIMESTAMP,:public, :colorblind, :bgcolor,
		 :allowties, :showOriginal, :samePair, :horizontalflip,
		 :monitordistance, :lighttype, :naturallighting,
		 :screenluminance, :whitepoint, :whitepointroom,
		 :ambientIllumination, :person, :exType,
		 :timer, :invHash);";
		$sth = $db->prepare($sql);
		$sth->bindParam(':title',$experiment['title']);
		$sth->bindParam(':shortdesc', $experiment['shortDescription']);
		$sth->bindParam(':longdesc', $experiment['longDescription']);
		$sth->bindParam(':public', $experiment['isPublic']);
		$sth->bindParam(':colorblind', $experiment['allowColourBlind']);
		$sth->bindParam(':bgcolor', $experiment['backgroundColour']);
		$sth->bindParam(':allowties', $experiment['allowTies']);
		$sth->bindParam(':showOriginal', $experiment['showOriginal']);
		$sth->bindParam(':samePair', $experiment['samePair']);
		$sth->bindParam(':horizontalflip', $experiment['horizontalFlip']);
		$sth->bindParam(':monitordistance', $experiment['monitorDistance']);
		$sth->bindParam(':lighttype', $experiment['lightType']);
		$sth->bindParam(':naturallighting', $experiment['naturalLighting']);
		$sth->bindParam(':screenluminance', $experiment['screenLuminance']);
		$sth->bindParam(':whitepoint', $experiment['whitePoint']);
		$sth->bindParam(':whitepointroom', $experiment['whitePointRoom']);
		$sth->bindParam(':ambientIllumination', $experiment['ambientIllumination']);
		$sth->bindParam(':person', $_SESSION['user']['id']);
		$sth->bindParam(':exType', $experiment['experimentType']);
		$sth->bindParam(':timer', $experiment['timer']);
		$sth->bindParam(':invHash', $experiment['inviteHash']);
		$sth->execute();
		$newExperimentId = $db->lastInsertId();

		//CategoryExperiment
		if($experiment['experimentType'] == 3) {
			$data = file_get_contents($experimentId. "/categoryname.txt");
			$categoryname = json_decode($data, true);
			foreach($categoryname as $cn) {
				$sql = "INSERT INTO `categoryname` (`id`, `name`, `personId`, `standardFlag`) VALUES (NULL, :name, :personId, :standardFlag);";
				$sth = $db->prepare($sql);
				$sth->bindParam(':name', $cn['name']);
				$sth->bindParam(':personId', $_SESSION['user']['id']);
				$sth->bindParam(':standardFlag', $cn['standardFlag']);
				$sth->execute();
				$newCategoryNameId = $db->lastInsertId();

				$sql = "INSERT INTO `experimentcategory` (`id`, `category`, `experiment`) VALUES (NULL, :categoryId, :experiment);";
				$sth = $db->prepare($sql);
				$sth->bindParam(':categoryId', $newCategoryNameId);
				$sth->bindParam(':experiment', $newExperimentId);
				$sth->execute();
			}
		}

		$sql = "INSERT INTO `experimentqueue` (`id`, `experiment`) VALUES (NULL, :experiment);";
		$sth = $db->prepare($sql);
		$sth->bindParam(':experiment',$newExperimentId);
		$sth->execute();
		$NewExperimentQueue = $db->lastInsertId();

		foreach($infotype as $it) {
			$newInfotypeId = null;
			if($it['standardFlag'] == 1) {
				$sql = "SELECT * FROM infotype WHERE id = :id;";
				$sth = $db->prepare($sql);
				$sth->bindParam(':id', $it['id']);
				$sth->execute();
				if($sth->rowcount() == 1) {	//Already exists in database
					$result = $sth->fetch();
					$newInfotypeId = $result['id'];
				}
			}
			if($newInfotypeId == null) {
				$sql = "INSERT INTO `infotype` (`id`, `standardFlag`, `info`, `person`) VALUES (NULL, :standardFlag, :info, :person);";
				$sth = $db->prepare($sql);
				$sth->bindParam(':standardFlag',$it['standardFlag']);
				$sth->bindParam(':info', $it['info']);
				$sth->bindParam(':person', $_SESSION['user']['id']);
				$sth->execute();
				$newInfotypeId = $db->lastInsertId();
			}

			$sql = "INSERT INTO `experimentinfotype` (`id`, `experiment`, `infoType`) VALUES (NULL, :experiment, :infotype);";
			$sth = $db->prepare($sql);
			$sth->bindParam(':experiment', $newExperimentId);
			$sth->bindParam(':infotype',$newInfotypeId);
			$sth->execute();
		}

		$sortedPictures = sortPicturesToDatabase($picture, $pitureset, $db);

		foreach($experimentorder as $exOrder) {
			$newExOrder = $exOrder;
			$newExOrder['experimentQueue'] = $NewExperimentQueue;

			if($exOrder['pictureQueue'] != null) {


				$newPictureQueueId = insertPictureQueueToDatabase($picturequeue, $newExOrder['pictureQueue'], $db);
				foreach($pictureorder as $po) {
					//Pr�ver � finne matchendes bilde til loopet pictureQueue
					//Dersom $po matcher, s� skal den inn i databasen.
					if($po['picturequeue'] == $exOrder['pictureQueue']) {
						$newPictureId = findNewIdForPicture($sortedPictures, $po['picture']);	//Gets new ID for pictureOrder.picture

						$sql = "INSERT INTO `pictureorder`
								(`id`, `pOrder`, `picture`, `pictureQueue`)
								VALUES (NULL, :pOrder, :picture, :pictureQueue);";
						$sth = $db->prepare($sql);
						$sth->bindParam(':pOrder', $po['pOrder']);
						$sth->bindParam(':picture', $newPictureId);
						$sth->bindParam(':pictureQueue', $newPictureQueueId);
						$sth->execute();
					}
				}
				//Finn all pictureOrder som h�rer til pictureQueue
				//Sett disse inn i database med ny pictureQueue og ny pictureId.
				$newExOrder['pictureQueue'] = $newPictureQueueId;
			} else if($exOrder['instruction'] != null) {

				$newInstructionId = insertInstructionToDatabase($instruction, $newExOrder['instruction'], $db);
				$newExOrder['instruction'] = $newInstructionId;
			}
		insertExperimentOrderToDatabase($newExOrder, $db);
		}

		$db->commit();
		copyPicturesToCorrectFolder($sortedPictures, $targetDir, $db, $picture, $experiment);
		echo json_encode(1);
   	} catch(exception $e) {
   		echo json_encode(0);
   		$db->rollBack();
   	}

}
	else if($option == "cleanDirectory") {
		$path = "../../uploads/zipfiles/" . $_SESSION['user']['id']. "/";
		SureRemoveDir($path, true);
	}

	/**
	 * Will copy all pictures to their correct folder.
	 * This is done LAST, after we know that the database is ok, in case something
	 * goes wrong with the database.
	 */
	function copyPicturesToCorrectFolder($sortedPictureSet,$originalDir , $db, $oldPictures, $oldExperiment) {
		foreach($sortedPictureSet as $pictureSet) {
		mkdir("../../uploads/" . $_SESSION['user']['id'] ."/". $pictureSet['newPictureset'], 0777, true);
			foreach($pictureSet['picture'] as $pictureId) {
				$newTargetPath = getUrlForPicture($pictureId['newId'], $db);
				$oldTargetPath;
				foreach($oldPictures as $picture) {
					if($pictureId['oldId'] == $picture['id']) {
						$picture['person'] = $oldExperiment['person'];
						$oldTargetPath = "../../uploads/zipfiles/" . $_SESSION['user']['id'] .
						"/randomName/uploads/" . $oldExperiment['person'] . "/" . $pictureSet['oldPictureset']
						. "/" . $picture['name'];
					}
				}
				copy($oldTargetPath, $newTargetPath);
			}
		}

	$path = "../../uploads/zipfiles/" . $_SESSION['user']['id']. "/";
	SureRemoveDir($path, true);
}

	/**
	 * Deletes COMPLETE $dir
	 * @param $dir = link to a directory that will be deleted.
	 * @param $Deleteme = 1/0 if you want to delete the main directory.
	 * @author rahulnvaidya at gmail dot com
	 * http://www.php.net/manual/en/function.unlink.php
	 */
	function SureRemoveDir($dir, $DeleteMe) {
    if(!$dh = @opendir($dir)) return;
    while (false !== ($obj = readdir($dh))) {
        if($obj=='.' || $obj=='..') continue;
        if (!@unlink($dir.'/'.$obj)) SureRemoveDir($dir.'/'.$obj, true);
    }
    if ($DeleteMe){
        closedir($dh);
        @rmdir($dir);
    }
}
/**
 * Will generate URL to a given picture.
 * @param $picture = All information about a picture from the database in an array.
 * @return $url = path to the supplied picture.
 */
function generateUrl($picture){
		$index = strripos($picture[1],".");
		$fileType = substr($picture[1],$index, strlen($picture[1]));
		$url = "../../uploads/" . $picture['person'] . "/" . $picture[4] . "/" . $picture['url'] . $fileType;
		return $url;
	}


/**
 * Will get URL for a given pictureId.
 * @param $pictureId = ID from the database for the picture you want the URL from.
 * @param $db = PDO connection to the database.
 * @return $url complete URL for a pictureID.
 */
function getUrlForPicture($pictureId, $db) {
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
 * Will find the new ID from the database with the old supplied pictureId.
 * @param $sortedPictureSet = Array containing all pictures with both old and new id for pictures.
 * @param $oldPictureId = old ID for a given picture from the old database.
 * @return $newId int containing a new ID for the picture from the new database.
 */
	function findNewIdForPicture($sortedPictureSet, $oldPictureId) {
		$newId = 0;
		foreach($sortedPictureSet as $pictureSet) {
			foreach($pictureSet['picture'] as $picture) {
				if($picture['oldId'] == $oldPictureId) {
					$newId =  $picture['newId'];
					break;
				}
			}
		}
		return $newId;
	}

	/**
	 * Function will match pictureSets with the according pictures.
	 * @param $picturesArray Array containing pictures.
	 * @param pictureSetArray array containing all pictureSets
	 * @param $db PDO connection to database.
	 * @return A sorted array with pictureSets and pictures.
	 */
	function sortPicturesToDatabase($picturesArray, $pictureSetArray, $db) {
		$matchedPictureSets;
		$arr;
		$index = 0;
		foreach($pictureSetArray as $pictureSet) {		//Loop all pictureSets
			$arr[$index]['oldPictureset'] = $pictureSet['id'];
			$sql = "INSERT INTO `pictureset` (`id`, `name`, `text`, `pictureAmount`, `person`) VALUES (NULL, :name, :text, 0, :person);";
			$sth = $db->prepare($sql);
			$sth->bindParam(':name', $pictureSet['name']);
			$sth->bindParam(':text', $pictureSet['text']);
			$sth->bindParam(':person', $_SESSION['user']['id']);
			$sth->execute();
			$newPictureSetId = $db->lastInsertId();

			$arr[$index]['newPictureset'] = $newPictureSetId;

			foreach($picturesArray as $picture) {		//Will find all pictures for the looped set.
				if($picture['pictureSet'] == $pictureSet['id']) {
					$sql = "INSERT INTO `picture` (`id`, `name`, `url`, `isOriginal`, `pictureSet`) VALUES (NULL, :name, :url, :isOriginal, :pictureSet);";
					$sth = $db->prepare($sql);
					$sth->bindParam(':name', $picture['name']);
					$sth->bindParam(':url', $picture['url']);
					$sth->bindParam(':isOriginal', $picture['isOriginal']);
					$sth->bindParam(':pictureSet', $newPictureSetId);
					$sth->execute();

				    $p['oldId'] = $picture['id'];
					$p['newId'] = $db->lastInsertId();
					$arr[$index]['picture'][] = $p;

				    $sql = "UPDATE `pictureset` SET `pictureAmount` = pictureAmount+1 WHERE `pictureset`.`id` = :id;";
				    $sth = $db->prepare($sql);
				    $sth->bindParam(':id', $newPictureSetId);
				    $sth->execute();
				}
			}
			$index++;
		}
		return $arr;
	}
	/**
	 * Used for finding the new ID for a given old picture Id.
	 * @param $pictureArray an array containing the data from the experiment to be imported.
	 * @param $oldPictureId ID for a old picture.
	 * @return ID for the given picture.
	 */
	function getNewIdForPicture($pictureArray,$oldPictureId) {
		foreach($pictureArray as $picture) {
			if($picture['id'] == $oldPictureId) {
				return $picture['newId'];
				break;
			}
		}
	}

	/**
	 *  Will insert a given instruction into the database.
	 *  @param $instructionArray an array containing all instructions for an experiment.
	 *  @param $instructionID int with which id to insert to the database.
	 *  @param $db PDO connection to the database.
	 *  @retunr New ID from the database for instruction.
	 */
	function insertInstructionToDatabase($instructionArray, $instructionId, $db) {
		foreach($instructionArray as $instruction) {
			if($instruction['id'] == $instructionId) {
				$sql = "INSERT INTO `instruction` (`id`, `standardFlag`, `text`, `personId`) VALUES (NULL, :standardFlag, :text, :personId);";
				$sth = $db->prepare($sql);
				$sth->bindParam(':standardFlag', $instruction['standardFlag']);
				$sth->bindParam(':text', $instruction['text']);
				$sth->bindParam(':personId', $_SESSION['user']['id']);
				$sth->execute();
				return $db->lastInsertId();
				break;
			}
		}
	}
	/**
	 * Will insert a pictureQueue to the database.
	 * @param $pictureQueueArray an array containing all pictureQueues.
	 * @param $id ID for given pictureQUeue to insert.
	 * @param $db PDO connection to database.
	 * @return Newly inserted ID of the pictureQueue.
	 */
	function insertPictureQueueToDatabase($pictureQueueArray, $id, $db) {
			foreach($pictureQueueArray as $pictureQueue) {
				if($pictureQueue['id'] == $id) {
 					$sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, :title);";
					$sth = $db->prepare($sql);
					$sth->bindParam(':title',$pictureQueue['title']);
					$sth->execute();
					return $db->lastInsertId();
					break;
				}
			}
		}
	/**
	 * Will insert an experimentOrder into the database
	 * @param $experimentorder is an array containing all information for an experimentOrder
	 * @param $db PDO connection to the database.
	 */
	function insertExperimentOrderToDatabase($experimentOrder, $db) {
		$sql = "INSERT INTO `experimentorder` (`eOrder`, `pictureSet`, `experimentQueue`, `pictureQueue`, `instruction`)
				VALUES (NULL, NULL, :experimentQueue, :pictureQueue, :instruction);";
		$sth = $db->prepare($sql);
		$sth->bindParam(':experimentQueue', $experimentOrder['experimentQueue']);
		$sth->bindParam(':pictureQueue', $experimentOrder['pictureQueue']);
		$sth->bindParam(':instruction',$experimentOrder['instruction']);
		$sth->execute();
	}
?>
