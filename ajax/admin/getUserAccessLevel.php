<?php
/**
 * Gets a users accessLevel based on email.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    $stmt = $db->prepare("SELECT title FROM userType as a WHERE EXISTS (SELECT * FROM person as b WHERE b.email = :email AND b.userType = a.id)");
	
	$stmt->execute(array(':email' => $_POST['email']));
	
	$res = $stmt->fetchAll();

	
	echo json_encode($res);
    
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>