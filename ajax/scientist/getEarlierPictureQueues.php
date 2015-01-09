<?php
/**
 * Will get all pictureQueues for a given pictureSet.
 */
require_once('../../db.php');
include_once('../../functions.php');
if (!isset($_SESSION['user'])) {
               header("Location: ../../login.php"); 
            }
if($_SESSION['user']['userType'] > 2) {
	return;
}

$pictureSetId = $_GET['pictureSetId'];
$sql = "SELECT * FROM pictureset
	JOIN picture ON pictureset.id=picture.pictureset
	JOIN pictureorder ON picture.id=pictureorder.picture
	JOIN picturequeue ON pictureorder.picturequeue=picturequeue.id
	WHERE pictureset.id = ? AND picturequeue.title IS NOT NULL
	GROUP BY pictureorder.picturequeue;";
$sth = $db->prepare($sql);
$sth->bindParam(1,$pictureSetId);
$sth->execute();
$result = $sth->fetchAll();
$pictureQueue = Array();

foreach($result as $row) {
	$sqlm = "SELECT * FROM pictureQueue WHERE id = ?;";
	$sthm = $db->prepare($sqlm);
	$sthm->bindParam(1,$row['pictureQueue']);
	$sthm->execute();
	$res = $sthm->fetch();
	$pictureQueue[] = $res;
}
if(count($pictureQueue) == 0) {
	echo json_encode(0);
} else {
	echo json_encode($pictureQueue);
	
}
?>