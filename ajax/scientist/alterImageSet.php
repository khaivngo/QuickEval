<?php

/**
 * This file will alter imageSets, depending on which $_POST it gets.  
 * It will either increment the number of pictures in set, edit name, or edit text.
 */

require_once('../../db.php');
if (!isset($_SESSION['user'])) {
               header("Location: ../../login.php"); 
            }

	$option = $_POST['option'];
	
	if(isset($_POST['imagesetId'])) {
		$imagesetId = $_POST['imagesetId'];
	}
	
	if($option == "updateAmountOfPictures") {
		try {
			$amount = $_POST['amount'];
		    $sql = "UPDATE pictureset SET pictureAmount=(pictureAmount + ?) WHERE id = ?";
			$sth = $db->prepare($sql);
			$sth->bindParam(1, $amount);
			$sth->bindParam(2, $imagesetId);
			$sth->execute();
			echo json_encode("Updated amount of pictures"); //FJERN
			
		} catch (PDOException $excpt) {
			}
			
	} else if($option == "updateName"){
		try {
			$name = $_POST['name'];
		    $sql = "UPDATE pictureset SET name=? WHERE id = ?";
			$sth = $db->prepare($sql);
			$sth->bindParam(1, $name);
			$sth->bindParam(2, $imagesetId);  
			$sth->execute();
			echo json_encode("Updated name for imageset");
			
		} catch (PDOException $excpt) {
			}
			
	} else if($option == "updateText") {
		try {
		
			$text = $_POST['text'];
		    $sql = "UPDATE pictureset SET text=? WHERE id = ?";
			$sth = $db->prepare($sql);
			$sth->bindParam(1, $text);
			$sth->bindParam(2, $imagesetId);  
			$sth->execute();
			echo json_encode("Updated text for imageset");
		} catch (PDOException $excpt) {
			}
	
			
	} else if($option == "finishedUploading") {
		$_SESSION['user']['imagesetId'] = 0;	//"Resets" the imagesetId
		
	} else if ($option == 'getActiveImagesetId') {
		echo json_encode($_SESSION['user']['imagesetId']);
		
	} else if ($option =='updateOriginal') {
		try {
		$imageId = $_POST['idOfOriginal'];
		
		$sql = "SELECT * FROM picture WHERE id = ?";
		$sth = $db->prepare($sql);
		$sth->bindParam(1, $imageId);
		$sth->execute();
		$result = $sth->fetch();
		
		$sql = "UPDATE picture SET isOriginal=0 WHERE pictureSet = ?";
		$sthm = $db->prepare($sql);
		$sthm->bindParam(1, $result['pictureSet']);
		$sthm->execute();
		
		$sql = "UPDATE picture SET isOriginal=1 WHERE id = ?";
		$sthn = $db->prepare($sql);
		$sthn->bindParam(1, $imageId);
		$sthn->execute();
		
		echo json_encode("1");
		} catch(PDOException $excpt) {
			echo json_encode("0");
		}

	}
	

?>