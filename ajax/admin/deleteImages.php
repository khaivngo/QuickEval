<?php
/**
 * Used for deleting old pictures and pictureSets.
 */
require_once('../../db.php');
require_once('../../functions.php');

if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}  

$option = $_GET['option'];	//imageset, images
$access = $_SESSION['user']['userType'];
$userId = $_SESSION['user']['id'];

if (checkLogin($db) > 2) {
	exit;
}

try {      
	$arrayId = $_GET['imageArray'];

	if ($option =="images") {
		foreach($arrayId  as $imageId) {
			deletePicture($imageId, $db, $access, $userId,0);
		}
		echo json_encode(1);
		exit;
	}
	else if($option =="imageset") {
		 foreach($arrayId as $imagesetId) {
		 	//If admin or owner of set.
		 	if (checkOwnerOfSet($imagesetId, $db, $userId) == 1 || $access == 1) {
				 $sql = "SELECT * FROM picture WHERE pictureSet = ?;";
				 $sth = $db->prepare($sql);
				 $sth->bindParam(1,$imagesetId);
				 $sth->execute();
				 $result = $sth->fetchAll();
				 
				 foreach($result as $image) {
					 deletePicture($image['id'], $db, $access,$userId,1);
				 }
				 
				 $sql = "DELETE FROM pictureset WHERE id = ?";
				 $sth = $db->prepare($sql);
				 $sth->bindParam(1,$imagesetId);
				 $sth->execute();
				 rmdir("../../uploads/" . $userId . "/" . $imagesetId);
				 
				 $sql = "SELECT * FROM pictureset WHERE person = ?;";
				 $sth = $db->prepare($sql);
				 $sth->bindParam(1, $userId);
				 $sth->execute();
				 if ($sth->rowCount() == 0) {
				 	rmdir("../../uploads/" . $userId);
				 }
				 
				 echo json_encode(1);
				 exit;
		 	}
		 }
	}
} catch (Exception $ex) {
	echo json_encode(0);
	exit;
}

/**
 * Will delete a given picture
 * @param $imageid = ID for the picture you want deleted.
 * @param $db = PDO connection to the database.
 * @param $access = AccessLevel for logged in user.
 * @param $userId = $userId for logged in user.
 * @param $deleteOriginal true/false if you want to delete the original photo.
 */
function deletePicture($imageId, $db, $access, $userId, $deleteOriginal) {	
	try {
			$filePath = getFilePath($imageId, $db, $userId);
			$sql = "SELECT * FROM picture
					JOIN pictureset ON picture.pictureset = pictureset.id
					WHERE picture.id = ?;";
			$sth = $db->prepare($sql);
			$sth->bindParam(1,$imageId);
			$sth->execute();
			$result = $sth->fetch();	
			$imagesetId = $result['id'];
			
			/**
			 * If user owns picture, OR is admin with accesslevel 1
			 */
			if (($result['person'] == $userId || $access == 1) && isset($result['pictureSet'])) {
				$sql2 = "SELECT * FROM pictureOrder WHERE picture = ?";
				$sth2 = $db->prepare($sql2);
				$sth2->bindParam(1, $imageId);
				$sth2->execute();
				/**
				 * Deletes picture if it doesnt exist in a pictureOrder *
				 */									  
				if (!$sth2->fetch()) {
					//Picture is not in a set, will then be deleted!
					$sql3 = "";
					//Happens when deleting single images
					if ($deleteOriginal == 0)	{
						$sql3 = "DELETE FROM picture WHERE id = ? AND isOriginal = 0";
					}
					//Happens when deleting whole imagesets
					else if ($deleteOriginal == 1) {
						$sql3 = "DELETE FROM picture WHERE id = ?";
					}
					$sth3 = $db->prepare($sql3);
					$sth3->bindParam(1,$imageId);
					$sth3->execute();
					
					//Checks if the picture got deleted
					$sql4 = "SELECT * FROM picture WHERE id = ?";
					$sth4 = $db->prepare($sql);
					$sth4->bindParam(1,$imageId);
					$sth4->execute();
					
					if($sth4->rowCount() == 0) {	//Picture got deleted!
						$sql5 = "UPDATE pictureset SET pictureAmount=(pictureAmount-1) WHERE id = ?";
						$sth5 = $db->prepare($sql5);
						$sth5->bindParam(1,$imagesetId);
						$sth5->execute();
						unlink($filePath);
					}
				}
			}
	} catch (Exception $ex) {
		echo json_encode(0);
		exit;
  }
}

/**
 * Gets file path for a given image.
 * @param $imageId = ID for the picture you want filePath for.
 * @param $db = PDO connection to the database.
 * @param $userId = UserId for logged in user.
 * @return string containing path for a picture.
 */
function getFilePath($imageId, $db, $userId) {
	$sql = "SELECT * FROM picture WHERE id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$imageId);
	$sth->execute();
	$result = $sth->fetch();

	$url = $result['url'];
	$index = strripos($result['name'],".");
	$fileType = substr($result['name'],$index, strlen($result['name']));

	return "../../uploads/" . $userId . "/" . $result['pictureSet'] . "/" . $result['url'] . $fileType;
}

/**
 * Checks if the logged in user owns a given pictureSet
 * @param $imagesetId = ID for the pictureSetId you want to check owner of.
 * @param $userId = UserId for logged in user.
 * @param $db = PDO connection to the database.
 * @return 1/0 if the logged in user owns the pictureSet.
 */
function checkOwnerOfSet($imagesetId, $db, $userId) {
	$sql = "SELECT * FROM pictureset WHERE id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $imagesetId);
	$sth->execute();
	$result = $sth->fetch();
	
	if ($result['person'] == $userId) {
		return 1;
	} else {
		return 0;
	}
}

/**
 * Checks if the logged in user owns a given picture
 * @param $imageId = ID for the image you want to check owner of.
 * @param $userId = UserId for logged in user.
 * @param $db = PDO connection to the database.
 * @return 1/0 if the logged in user owns the pictureSet.
 */
function checkOwnerOfPicture($imageId, $db, $userId) {
	$sql = "SELECT * FROM picture WHERE id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $imageId);
	$sth->execute();
	$result = $sth->fetch();
	
	return checkOwnerOfSet($result['pictureSet'], $db,$userId);
}

?>
