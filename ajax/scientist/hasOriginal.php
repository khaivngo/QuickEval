<?php
/**
 * Will check if a file has a original picture.
 */
require_once('../../db.php');
include_once('../../functions.php');

if (!isset($_SESSION['user'])) {
	header("Location: ../../login.php");
	exit;
}

if($_SESSION['user']['userType'] > 2) {
	return;
}

$pictureSetId = $_GET['pictureSetId'];

$sql = "SELECT pictureset.id FROM pictureset " .
	"JOIN picture ON picture.pictureSet = pictureset.id " .
	"WHERE pictureset.id = ? AND picture.isOriginal = 1";
$sth = $db->prepare($sql);
$sth->bindParam(1,$pictureSetId);
$sth->execute();

if($sth->rowCount() > 0) {
	echo json_encode();
} else {
	echo json_encode(0);
}
?>
