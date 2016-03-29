<?php 

require_once "tempdb/DBconnect.php";

$imgSrc = $_POST['imgSrc'];
$shape = json_decode($_POST['shapeItem']);
$counter = 0;
$saved = false;

ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);
	
$annotation = $shape->annotation;
$fill = json_encode($shape->fill);
	
$query = "INSERT INTO shape (image_src, fill, annotation) VALUES (?,?,?)";

$sth = $db->prepare($query);				// Prepare the query.

while(!$saved)
{	
	$counter ++;
	if( $sth->execute(array( $imgSrc, $fill, $annotation )) );
		$saved=true;
}	

if($saved)
	echo $counter;




	