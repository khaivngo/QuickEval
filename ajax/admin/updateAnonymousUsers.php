<?php
/**
 * This file is used by an admin to delete all old photos based on a given interval.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');
if($access > 1) {	//3/4 = observer/anonymous
	return;
}
$selection = $_POST['selection'];

global $interval;

if(checkLogin > 2) {
 return;
}

if (strlen($selection) < 2) {                   //user has chosen from the dropdown menu with predifined intervals.
    switch ($selection) {
        case '1':
            $interval = "1 WEEK";
            break;
        case '2':
            $interval = "1 MONTH";
            break;
        case '3':
            $interval = "6 MONTH";
            break;
        case '4':
            $interval = "1 YEAR";
            break;

        default:
    }
} else {                        //user has either input custom date or used datepicker
 //   ChromePhp::log("user used DATAPICKER");
    $now = time();
    $myTime = strtotime($selection);
    $dateDiff = $now - $myTime;
    (int) $days = ($dateDiff / (60 * 60 * 24));

    try {
        $stmt = $db->exec("DELETE FROM person WHERE creationDate < DATE_SUB(NOW(), INTERVAL '" . $days . "' DAYS) AND userType = 5");
        echo json_encode($stmt);
    } catch (Exception $ex) {
        // ChromePhp::log($ex->getMessage());
    }
}

if ($interval == 0) {                            //if user has opted to just purge the table for anonymyous users
    try {
        $stmt = $db->exec("DELETE FROM person WHERE userType = 5");
        // ChromePhp::log($stmt);
        echo json_encode($stmt);
    } catch (Exception $ex) {
       //  ChromePhp::log($ex->getMessage());
    }
} else {                //
    try {
        $stmt = $db->exec("DELETE FROM person WHERE creationDate < DATE_SUB(NOW(), INTERVAL '" . $interval . "') AND userType = 5");
        echo json_encode($stmt);
       //  ChromePhp::log("Update complete");          //for logging purposes
    } catch (Exception $ex) {
       //  ChromePhp::log($ex->getMessage());
    }
}
?>