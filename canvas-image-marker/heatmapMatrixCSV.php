<?php
 
$matrix = $_POST['matrix'];
$filename = $_POST['fileName'] . '.csv';
$file = fopen($filename, "w");

fwrite($file, $matrix); 
