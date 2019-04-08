<?php

/**
 * Gets results for an pair-experiment.
 * @return experimentsresults or 0 if none exist.
 */
require_once('../../db.php');
require_once('../../functions.php');
if (!isset($_SESSION['user'])) {
    header("Location: ../../login.php");
    exit;
}

try {
    $experimentId = $_POST['experimentId'];
    $result = getExperimentResults($experimentId, $db, 0);
    echo json_encode($result);
} catch (PDOException $excpt) {
    echo json_encode(0);
}
?>