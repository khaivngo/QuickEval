<?php

/***
 Delete 2 day old files
***/
$files = glob('*.csv');
$now   = time();

foreach($files as $file) {
    if (is_file($file)) {
        if ($now - filemtime($file) >= 60 * 60 * 24 * 2) { // 2 days
            unlink($file);
        }
    }
}

/***
 Write matrix to file
***/
$matrix = $_POST['matrix'];
$filename = $_POST['fileName'] . '.csv';
$file = fopen($filename, "w");

fwrite($file, $matrix);
