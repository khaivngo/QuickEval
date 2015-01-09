<?php
/**
 * Will change the name of a picture in the database.
 */
require_once('../../db.php');
if($_SESSION['user']['userType'] > 2) {
	return;
}

try {                                   //updates title of picture.		
	$stmt = $db->prepare("UPDATE picture SET name = :name WHERE id = :pictureId");

    $stmt->execute(array(	':name' => $_POST['name'],
							':pictureId' => $_POST['pictureId']));
							
} catch (Exception $ex) {
}
?>