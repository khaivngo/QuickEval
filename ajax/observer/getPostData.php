<?php

/**
 * Gets active experiment.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$experimentId = $_SESSION['experimentId'];

echo json_encode($experimentId);

?>