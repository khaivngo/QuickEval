<?php

require_once("tempdb/DBconnect.php");

$items = array();

$query = "SELECT id, image_src, fill, annotation FROM shape";

$sth = $db->prepare($query);					// Prepare the query.
$sth->execute();	// Execute the query.

$index = 0;

while($row = $sth->fetch(PDO::FETCH_OBJ))	// Fetch all rows as array
{
	$items[] = $row;
	
	/* $items[$index]->shapeObj = json_encode($row->shapeObj);
	
	$index ++; */
}
if( count($items) > 0)
	echo json_encode($items);
else
	echo "%uhm oh, there're no shapes yet";