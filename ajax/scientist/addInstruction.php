<?php
/**
 * Will insert a instruction to the database.
 */
require_once('../../db.php');

	$stmt = $db->prepare("INSERT INTO instruction (text, personId) VALUES (:text, :personId)");

	$stmt->execute(array(':text' => $_POST['text'],
		':personId' => $_SESSION['user']['id']));

	$id = $db->lastInsertId();


	$stmt = $db->prepare("INSERT INTO experimentorder (experimentQueue, instruction) VALUES (:experimentQueue, :instruction)");

	$stmt->execute(array(':experimentQueue' => $_POST['experimentQueue'],
		':instruction' => $id));
	
	echo json_encode(1);
?>