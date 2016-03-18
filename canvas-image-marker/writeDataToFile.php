<?php

$data = json_decode($_POST['array']); 


	// open file for write
$file = fopen("data.txt", "w") or die("Can not open the file!");

for ($i = 0; $i < count($data); $i++) 
{
	
	fwrite($file, $data[$i][0] . ', ' . $data[$i][1]);
}  

echo "hei";

// close the file
fclose($file);