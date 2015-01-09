<?php
/**
 * Will add to the databasetable "experimentinstruction".
 */
require_once('../../db.php');
if($_SESSION['user']['userType'] > 2) {
	return;
}

try {

	$stmt = $db->prepare("INSERT INTO experimentorder (experimentQueue, instruction) VALUES (:experimentQueue, :instruction)");

	$stmt->execute(array(':experimentQueue' => $_POST['experimentQueue'],
	               ':instruction' => $_POST['id']));
	 
	echo json_encode(1);

} catch (PDOException $excpt) {
}
?>