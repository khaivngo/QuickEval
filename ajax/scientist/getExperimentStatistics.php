<?php
/**
 * Will get experimenStatistics for a given experiment.
 */
require_once('../../db.php');

$userId = $_SESSION['user']['id'];             //fetching user id from session


try {
    $data = array();
    $available;

    //selects the three latest experiments made by user.
    $stmt = $db->query("SELECT id, title, person FROM experiment WHERE person = '" . $userId . "'"
            . "	ORDER BY ID DESC\n"
            . "	LIMIT 3");

    $res = $stmt->fetchAll();

    //settin retrieved id's
    $id1 = $res['0']['0'];
    $id2 = $res['1']['0'];
    $id3 = $res['2']['0'];


//    echo $id1;
//    echo $id2;
//    echo $id3;
    //checks if id's are set
    if (isset($id3) && $id3 > 1) {
        $available = 1;
        $data['title1'] = $res['0']['1'];        //setting title for return data
        $data['title2'] = $res['1']['1'];        //setting title for return data
        $data['title3'] = $res['2']['1'];        //setting title for return data
        // echo "3 exp";
    } elseif (isset($id2) && $id2 > 1) {
        $available = 2;
        $data['title1'] = $res['0']['1'];        //setting title for return data
        $data['title2'] = $res['1']['1'];        //setting title for return data
        echo "2 exp";
    } elseif (isset($id1) && $id1 > 1) {
        $available = 3;
        $data['title1'] = $res['0']['1'];        //setting title for return data
        echo "1 exp";
    } else {
        throw new Exception();      //if none is set means scientist doesn't have any, exits code.
    }


//---------------------Total visitors---------------------------------------------------------------------------------------

    for ($i = 1; $i <= 3; $i++) {

        // echo ${'id'.$i};
        //echo "Runned" . $i . "\n";

        if (!isset(${'id' . $i})) {
            echo "breaking out of for loop";
            break;
        }

        $stmt = $db->query("select COUNT(DISTINCT person) as total FROM experimentresult\n"
                . "WHERE experiment = '" . ${'id' . $i} . "'");

        $res = $stmt->fetchAll();

        $data['visitors' . $i] = $res;
    }

//--------------------Total completions----------------------------------------------------------------------------------------

    for ($i = 1; $i <= 3; $i++) {

        // echo ${'id'.$i};
        //echo "Runned" . $i . "\n";

        if (!isset(${'id' . $i})) {
            echo "breaking out of for loop";
            break;
        }

        $stmt = $db->query("SELECT COUNT(complete) AS total FROM experimentresult\n"
                . " WHERE experiment = '" . ${'id' . $i} . "' AND complete = \"1\"\n");

        $res = $stmt->fetchAll();

        $data['completion' . $i] = $res;
    }


//-------------------Average time-----------------------------------------------------------------------------------------


    for ($i = 1; $i <= 3; $i++) {

        // echo ${'id'.$i};
        //echo "Runned" . $i . "\n";

        if (!isset(${'id' . $i})) {
            echo "breaking out of for loop";
            break;
        }

        $stmt = $db->query("SELECT  TIME_TO_SEC(TIMEDIFF(endTime, startTime)) timeDiff FROM experimentresult \n"
                . "	WHERE experiment = '" . ${'id' . $i} . "' AND complete = \"1\"");

        $results = $stmt->fetchAll();

        //  print_r($res1[0]['timeDiff']);

        $totalTime = 0;
        $counter = 0;
        foreach ($results as $row) {

            if ($row['timeDiff'] != NULL) {
                $difference = $row['timeDiff'];
                // echo "\n\nTime difference in seconds " . $difference;
                $totalTime +=$difference;
                $counter++;
            }
        }

        if ($totalTime != 0) {

            $totalAverage = $totalTime / $counter;
            $average = gmdate("H:i:s", $totalAverage);
            $data['average' . $i] = $average;
        }
    }

//
//    $stmt = $db->query("SELECT  TIME_TO_SEC(TIMEDIFF(endTime, startTime)) timeDiff FROM experimentresult \n"
//            . "	WHERE experiment = '" . $id1 . "' AND complete = \"1\"");
//
//    $results = $stmt->fetchAll();
//
//
//    //  print_r($res1[0]['timeDiff']);
//    $totalTime = 0;
//    $counter = 0;
//    foreach ($results as $row) {
//        $difference = $row['timeDiff'];
//        // echo "\n\nTime difference in seconds " . $difference;
//
//        $totalTime +=$difference;
//        $counter++;
//    }
//
//    $totalAverage = $totalTime / $counter;
//
//    $average = gmdate("H:i:s", $totalAverage);
//
//    $data['average'] = $average;

    echo json_encode($data);  //returns them to ajax
    
} catch (Exception $ex) {
}
?>