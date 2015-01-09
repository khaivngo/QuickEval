<?php

require_once('../../db.php');
if (!isset($_SESSION['user'])) {
               header("Location: ../../login.php"); 
            }  

$option = $_GET['option'];	//imageset, images
$access = $_SESSION['user']['userType'];
$userId = $_SESSION['user']['id'];

if($access > 2) {	//3/4 = observer/anonymous
	return;
}

try {      
	$arrayId = $_GET['imageArray'];
	if($option =="images")
	{   	
		foreach($arrayId  as $imageId) {
			deletePicture($imageId, $db, $access, $userId,0);
		}
		echo json_encode(1);
	}
	
	else if($option =="imageset")
	{		 
		 foreach($arrayId as $imagesetId) 
		 {
		 	if(checkOwnerOfSet($imagesetId, $db, $userId) == 1 || $access == 1) 	//If admin or owner of set.
		 	{
				 $sql = "SELECT * FROM picture WHERE pictureSet = ?;";
				 $sth = $db->prepare($sql);
				 $sth->bindParam(1,$imagesetId);
				 $sth->execute();
				 $result = $sth->fetchAll();
				 
				 foreach($result as $image)
				 {
				 	deletePicture($image['id'], $db, $access,$userId,1);
				 }
				 
				 $sql = "DELETE FROM pictureset
				 		WHERE id = ?";
				 $sth = $db->prepare($sql);
				 $sth->bindParam(1,$imagesetId);
				 $sth->execute();
				 rmdir("../../uploads/" . $userId . "/" . $imagesetId);
				 
				 $sql = "SELECT * FROM pictureset WHERE person = ?;";
				 $sth = $db->prepare($sql);
				 $sth->bindParam(1, $userId);
				 $sth->execute();
				 if($sth->rowCount() == 0) {
				 	rmdir("../../uploads/" . $userId);
				 }
				 
				 echo json_encode(1);
		 	}
		 }
	}
	
	

} catch (Exception $ex) {
	echo json_encode(0);
	
}

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
			if(($result['person'] == $userId || $access == 1) && isset($result['pictureSet'])) 
			{	
				$sql2 = "SELECT * FROM pictureOrder 
						WHERE picture = ?";
				$sth2 = $db->prepare($sql2);
				$sth2->bindParam(1, $imageId);
				$sth2->execute();
				/**									 					**\
				 * Deletes picture if it doesnt exist in a pictureOrder *
				 */									  
				if(!$sth2->fetch()) 
				{	
					//Picture is not in a set, will then be deleted!
					$sql3 = "";
					if($deleteOriginal == 0)	{ 				//Happens when deleting single images
						$sql3 = "DELETE FROM picture
								WHERE id = ? AND isOriginal = 0";
					}
					else if($deleteOriginal == 1) {				//Happens when deleting whole imagesets
						$sql3 = "DELETE FROM picture
								WHERE id = ?";
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
						$sql5 = "UPDATE pictureset
								SET pictureAmount=(pictureAmount-1)
								WHERE id = ?";
						$sth5 = $db->prepare($sql5);
						$sth5->bindParam(1,$imagesetId);
						$sth5->execute();
						unlink($filePath);
					} 
					
				}
				
			}
	} catch (Exception $ex) {
		json_encode(0);
  }
}

function getFilePath($imageId, $db, $userId) {
	$sql = "SELECT * FROM picture WHERE id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$imageId);
	$sth->execute();
	$result = $sth->fetch();
	
	$url = $result['url'];
	$index = strripos($result['name'],".");
	$fileType = substr($result['name'],$index, strlen($result['name']));
	
	return("../../uploads/" . $userId . "/" . $result['pictureSet'] . "/" . $result['url'] . $fileType);
}

function checkOwnerOfSet($imagesetId, $db, $userId) {
	$sql = "SELECT * FROM pictureset WHERE id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $imagesetId);
	$sth->execute();
	$result = $sth->fetch();
	
	if($result['person'] == $userId) {
		return 1;
	} else {
		return 0;
	}
}

function checkOwnerOfPicture($imageId, $db, $userId) {
	$sql = "SELECT * FROM picture WHERE id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $imageId);
	$sth->execute();
	$result = $sth->fetch();
	
	return checkOwnerOfSet($result['pictureSet'], $db,$userId);
}

?>

