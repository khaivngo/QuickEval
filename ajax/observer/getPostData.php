<?php

/**
 * Gets active experiment.
 */
require_once('../../db.php');

$experimentId = $_SESSION['experimentId'];

echo json_encode($experimentId);
exit;

?>