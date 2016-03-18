<?php 

require_once "tempdb/DBconnect.php";

$imgSrc = $_POST['imgSrc'];
$savedShapes = json_decode($_POST['savedShapes']);

//$points = "";
$counterCheck =0;

for($i = 0; $i < count($savedShapes); $i++)
{
	/* for($j = 0; $j < count($savedShapes[$i]->fill); $j++ )
	{
		$points .= $savedShapes[$i]->fill[$j]->x . ', ' . $savedShapes[$i]->fill[$j]->y . ', ';
	} */
	
	$annotation = $savedShapes[$i]->annotation;
	$fill = json_encode($savedShapes[$i]->fill);
		
	$query = "INSERT INTO shape (image_src, fill, annotation) VALUES (?,?,?)";

	$sth = $db->prepare($query);				// Prepare the query.
	if($sth->execute(array( $imgSrc, $fill, $annotation )) )// Execute the query.
		$counterCheck++;
}

echo $counterCheck;


	