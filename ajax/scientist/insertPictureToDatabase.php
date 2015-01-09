<?php
/**
 * Used for inserting a picture into the database.
 */
require_once('../../db.php');
if (!isset($_SESSION['user'])) {
               header("Location: login.php"); 
            }
if($_SESSION['user']['userType'] > 2) {
	return;
}
try {

    $fileName = $_GET['fileName'];
    $imagesetId = $_GET['pictureSetId'];
    $url = $_GET['url'];
	$isOriginal = $_GET['isOriginal'];
		
	if($isOriginal == 1) {	//To avoid duplications.
		$sql = "UPDATE picture SET isOriginal=0 WHERE pictureSet = ?";
		$sthm = $db->prepare($sql);
		$sthm->bindParam(1, $imagesetId);
		$sthm->execute();
	}
		
    $sql = "INSERT INTO `picture` (`id`, `name`, `isOriginal`, `pictureSet`, `url`) VALUES (NULL, ?, ?, ?,?);";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $fileName);
	$sth->bindParam(2, $isOriginal);
	$sth->bindParam(3, $imagesetId);
	$sth->bindParam(4, $url);
	$sth->execute();
	
    echo json_encode("1");
    
} catch (PDOException $excpt) {
	echo json_encode("0");
}
?>