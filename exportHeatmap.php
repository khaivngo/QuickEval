<?php
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="heatmap.csv"');

require_once "classes/DB.php";

$experiment_id = $_GET['id'];
$picture_id = $_GET['pictureID'];
$picture_queue = $_GET['pictureQueue'];
$picture_name = $_GET['pictureName'];

$marks = DB::instance()->run_query(
    "SELECT * FROM artifactmark WHERE picture_id = ? AND picture_queue = ?", [
        $picture_id, $picture_queue
    ]
)->get_results();

foreach ($marks as $mark) {
    $shapes[] = json_decode( $mark->marked_pixels );
}


/*
*
*
* CREATE HEATMAP STRUCTURE HERE OR WHATEVER
*
*
*/



$fp = fopen('php://output', 'w');

# prints to file
foreach ($data as $line) {
	fwrite($fp, $line.PHP_EOL);
}

fclose($fp);
