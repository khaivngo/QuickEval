<?php
/**
 * upload.php
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */
include_once("db.php");


if (!isset($_SESSION['user'])) {
               header("Location: login.php"); 
            }
echo "swag";
// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// 5 minutes execution time
@set_time_limit(5 * 60);

$userid = $_SESSION['user']['id']; //$_POST['userName'];  Eller get session shit
if($_GET['imagesetId'] > 0) {	//This only occurs when pictures are being uploaded to an existing imageset.
	$imageSetId = $_GET['imagesetId'];
	echo "</br>Henter imagesetId fra GET = $imageSetId";
	
} else if(isset($_SESSION['user']['imagesetId']) && ($_SESSION['user']['imagesetId'] > 0)) {		 //Checks if there is an active imageSet up.
	$imageSetId = $_SESSION['user']['imagesetId'];	
	echo "</br>Henter imagesetId fra SESSION = $imagesetId";
	  										 	     //And gets its id
} else {		//Imageset is not active
	echo "</br>Kjorer return, noe gikk feil";
	return;		//Something went wrong, and imageset is not active.
}

$targetDir = 'uploads' . '/' . $userid . '/' . $imageSetId ;
echo "Targetdir is = " . $targetDir . "</br>";

$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds


// Create target dir
if (!file_exists($targetDir)) {
	@mkdir($targetDir,0777, true);
}

$type = $_FILES["file"]["type"];
$pos = strpos($type, "/");
$type = substr($type, $pos+1, strlen($type));

$fileName = $_GET['url'] . "." . $type;
$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

echo "\nFilePath = " . $filePath;

// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


// Remove old temp files	
if ($cleanupTargetDir) {
	if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	while (($file = readdir($dir)) !== false) {
		$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		// If temp file is current file proceed to the next
		if ($tmpfilePath == "{$filePath}.part") {
			continue;
		}

		// Remove temp file if it is older than the max age and is not the current file
		if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			@unlink($tmpfilePath);
		}
	}
	closedir($dir);
}	


// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
	if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	}

	// Read binary input stream and append it to temp file
	if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
} else {	
	if (!$in = @fopen("php://input", "rb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	}
}

while ($buff = fread($in, 4096)) {
	fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
	echo "Succesfully uploaded";
	// Strip the temp .part suffix off 
	rename("{$filePath}.part", $filePath);
}

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
