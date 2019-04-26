<?php
/**
 * This php file will create a new imageset, and then return the ID from the newly created imageset
 * Pictures uploaded will then use this imagesetId when getting stored in the database.
 */

require_once('../../db.php');

if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}
            
if(isset($_GET['stopImageset'])) {
		$_SESSION['user']['imagesetId'] = 0;
		exit;
}

try {
	$person = $_SESSION['user']['id'];
	$picAmount = 0;						//This variable is updated in the file updatePictureSet.php
	$text = $_GET['text'];
	$name = $_GET['name'];
	
	$sql = "INSERT INTO `pictureset` (`id`, `name`, `text`, `pictureAmount`, `person`) VALUES (NULL, ?, ?, ? ,?);";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $name);
	$sth->bindParam(2, $text);
	$sth->bindParam(3, $picAmount);
	$sth->bindParam(4, $person);    
	$sth->execute();

	$sql = "
		SELECT id FROM pictureset
		WHERE person = ?
		ORDER BY id DESC LIMIT 1
	";
	$stmt = $db->prepare($sql);
	$stmt->bindParam(1, $person);
	$stmt->execute();
	$result = $stmt->fetch();	//Gets recently inserted imagesetId to use for all the pictures
	
	$_SESSION['user']['imagesetId'] = $result['id'];	//Stores imagesetId on serverside.

	echo json_encode("1");
	exit;
} catch (PDOException $excpt) {
	echo json_encode("0");
	exit;
}

?>