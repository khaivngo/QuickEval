<?php
/**
 * Will get all pictures in a picturset.
 */
require_once('../../db.php');
if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}
            
       
$option = $_POST['option'];
          
/**
 * This function will get the recently uploaded pictures url.
 */
if ($option == "getUploadedPictures") {
		$imagesetId = $_POST['imagesetId'];
    $sql = "SELECT * FROM picture WHERE pictureSet = ?
	    		ORDER BY isOriginal DESC";
		$sth = $db->prepare($sql);
		$sth->bindParam(1, $imagesetId);  
		$sth->execute();
		
		$result = $sth->fetchAll();
		$urls = array();
		
		$userId = $_SESSION['user']['id'];
		
		$position = 0;
		foreach($result as $row) {
			$index = strripos($row['name'],".");
			$fileType = substr($row['name'],$index, strlen($row['name']));
			
			$urls[$position]['url'] = "uploads/" . $userId . "/" . $imagesetId . "/" . $row['url'] . $fileType; 	
			$urls[$position]['id'] = $row['id'];
			$urls[$position]['name'] = $row['name'];
			$position++;
		}
		echo json_encode($urls);
		exit;
}

?>