<?php
/**
 * Counts a total of users and returns it.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

try {
  $stmt = $db->query(
    "SELECT usertype.title, count(userType) as total FROM person "
    . "JOIN usertype "
    . "ON usertype.id = person.userType "
    . "GROUP by userType"
  );

	$res = $stmt->fetchAll();

	echo json_encode($res);
  exit;
} catch (PDOException $excpt) {
  // ChromePhp::log($excpt->getMessage());
}

?>