<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');


try {                                   //updates title of picture.		
	$stmt = $db->prepare("UPDATE picture SET name = :name WHERE id = :pictureId");

    $stmt->execute(array(	':name' => $_POST['name'],
							':pictureId' => $_POST['pictureId']));
							
} catch (Exception $ex) {
    ChromePhp::log($ex->getMessage());
}
?>