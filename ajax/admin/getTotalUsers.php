<?php
/**
 * Counts a total of users and returns it.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    $stmt = $db->query("SELECT usertype.title, count(userType) as total FROM person\n"
    . "JOIN usertype\n"
    . "	ON usertype.id = person.userType\n"
    . "GROUP by userType");
	
	
	$res = $stmt->fetchAll();

	echo json_encode($res);
    
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}

?>