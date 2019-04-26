<?php
/**
 * Will get experimenStatistics for a given experiment.
 */
require_once('../../db.php');

$userId = $_SESSION['user']['id'];
$experimentId = json_decode($_POST["experimentId"]);

try {
    $data = array();
    $available;

    //selects the three latest experiments made by user.
    $stmt = $db->query(
        "SELECT id, title, person FROM experiment WHERE person = '" . $userId . "' AND experiment.id = '" . $experimentId . "'"
        . "	ORDER BY ID DESC\n"
        . "	LIMIT 1"
    );

    $res = $stmt->fetchAll();

    //settin retrieved id's
    $id1 = $res['0']['0'];

    if (isset($id1) && $id1 > 1) {
        $available = 3;
        $data['title1'] = $res['0']['1'];        //setting title for return data
    } else {
        throw new Exception();      //if none is set means scientist doesn't have any, exits code.
    }


//---------------------Total visitors------------------------------

    $i = 1;

    if (!isset(${'id' . $i})) {
        echo "breaking out of for loop";
        break;
    }

    $stmt = $db->query("select COUNT(DISTINCT person) as total FROM experimentresult\n"
            . "WHERE experiment = '" . ${'id' . $i} . "'");

    $res = $stmt->fetchAll();

    $data['visitors' . $i] = $res;


//--------------------Total completions----------------------------------------------------------------------------------------


    if (!isset(${'id' . $i})) {
        echo "breaking out of for loop";
        break;
    }

    $stmt = $db->query(
        "SELECT COUNT(complete) AS total FROM experimentresult\n"
        . " WHERE experiment = '" . ${'id' . $i} . "' AND complete = \"1\"\n"
    );

    $res = $stmt->fetchAll();

    $data['completion' . $i] = $res;
//  }


//-------------------Average time-----------------------------------------------------------------------------------------


    if (!isset(${'id' . $i})) {
        echo "breaking out of for loop";
        break;
    }

    $stmt = $db->query(
        "SELECT  TIME_TO_SEC(TIMEDIFF(endTime, startTime)) timeDiff FROM experimentresult \n"
        . "	WHERE experiment = '" . ${'id' . $i} . "' AND complete = \"1\""
    );

    $results = $stmt->fetchAll();


    $totalTime = 0;
    $counter = 0;
    foreach ($results as $row) {

        if ($row['timeDiff'] != NULL) {
            $difference = $row['timeDiff'];
         
            $totalTime +=$difference;
            $counter++;
        }
    }

    if ($totalTime != 0) {
        $totalAverage = $totalTime / $counter;
        $average = gmdate("H:i:s", $totalAverage);
        $data['average' . $i] = $average;
    }

    echo json_encode($data);
    exit;
} catch (Exception $ex) {
}
?>