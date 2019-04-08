<?php

/**
 * Get all instructions belonging to a specific experiment.
 */

require_once "../../db.php";
include_once "../../functions.php";

if (!isset($_SESSION['user'])) {
   header("Location: ../../login.php");
   exit;
}

// if ($_SESSION['user']['userType'] > 2) {
//   exit;
// }

$experiment_id = isset($_POST['experimentId']) ? $_POST['experimentId'] : 0;

if ($experiment_id !== 0) {

  $stmt = $db->prepare(
    "SELECT DISTINCT experiment.title, experiment.id, experimentorder.instruction, instruction.text, instruction.id " .
    " FROM experiment AS origin, experimentqueue " .
    " JOIN experiment ON experiment.id = experimentqueue.experiment " .
    " JOIN experimentorder ON experimentqueue.id = experimentorder.experimentqueue " .
    " JOIN instruction ON instruction.id = experimentorder.instruction " .
    " WHERE experiment.id = ? AND experiment.person = ?"
  );

  $stmt->bindParam(1, $experiment_id);
  $stmt->bindParam(2, $_SESSION['user']['id']);
  $stmt->execute();
  $res = $stmt->fetchAll(); // PDO::FETCH_OBJ

  echo json_encode($res);
  exit;
}

?>
