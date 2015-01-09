<?php
/**
 * Will get ALL categories for a given experiment.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');


$option = $_GET['option'];
if($option == "getCategoriesForExperiment") {
	try {
		$experimentId = $_GET['experimentId'];
		$sql = "SELECT categoryname.name,categoryname.id FROM categoryname
				JOIN experimentcategory ON experimentcategory.category = categoryname.id
				WHERE experimentcategory.experiment = ?;"; 
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$experimentId);
		$sth->execute();
		$result = $sth->fetchAll();
		if(count($result) > 0) {
			echo json_encode($result);
		} else {
			echo json_encode(0);
		}
	} catch (PDOException $excpt) {
		echo json_encode(0);
	}
}
?>