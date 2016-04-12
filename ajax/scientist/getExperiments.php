<?php
/**
* Gets results for an pair-experiment
*/

require_once('../../db.php');
if (!isset($_SESSION['user'])) {
	header("Location: ../../login.php");
	exit;
}

try {
	$sql = "SELECT id, title, isPublic FROM experiment WHERE person = ?";

	$sth = $db->prepare($sql);
	$sth->bindParam(1,$_SESSION['user']['id']);
	$sth->execute();

	$result = $sth->fetchAll();
	echo json_encode($result);

} catch(PDOException $excpt) {
	echo json_encode("0");
}
?>
