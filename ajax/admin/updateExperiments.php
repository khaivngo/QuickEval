<?php
/**
 * This file is used by admins to delete or change old experiments based on a interval.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$selection = $_POST['selection'];
$userCheck = $_POST['userCheck'];
$access = $_SESSION['user']['userType'];
require_once('functions.php');

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
    ChromePhp::log("user used DATAPICKER");
    $now = time();
    $myTime = strtotime($selection);
    $dateDiff = $now - $myTime;
    (int) $days = ($dateDiff / (60 * 60 * 24));

    if ($userCheck == 0 && $access == 1) {          //there is no user check and user is admin
        try {
            $stmt = $db->exec("DELETE FROM experiment WHERE date < DATE_SUB(NOW(), INTERVAL '" . $days . "' DAYS)");
            echo json_encode($stmt);
        } catch (Exception $ex) {
            ChromePhp::log($ex->getMessage());
        }
    } else {            //user check is checked and user is not admin. Checks whether user is owner of experiment.
        try {
            $stmt = $db->exec("DELETE FROM experiment WHERE date < DATE_SUB(NOW(), INTERVAL '" . $days . "' DAYS) AND person = '" . $userId . "'");
            echo json_encode($stmt);
        } catch (Exception $ex) {
            ChromePhp::log($ex->getMessage());
        }
    }
}

if ($interval == 0 && $access == 1) {                            //if user has opted to just purge the table for experiments and is admin
    try {
        $stmt = $db->exec("TRUNCATE TABLE experiment");
        ChromePhp::log($stmt);
        echo json_encode($stmt);
    } catch (Exception $ex) {
        ChromePhp::log($ex->getMessage());
    }
} else {       //user is presumed not admin therefore a check on owner of experiment.
   
    if($userCheck == 1) {   //user check is required, checks if user is the owner of experiments
    
        try {
            $stmt = $db->exec("DELETE FROM experiment WHERE date < DATE_SUB(NOW(), INTERVAL '" . $interval . "' ) AND person = '" . $userId . "'");
            echo json_encode($stmt);
            ChromePhp::log("Update complete");          //for logging purposes
        } catch (Exception $ex) {
            ChromePhp::log($ex->getMessage());
        }
    } 
    elseif ($access == 1) {       //user is admin, deletes all experiments from chosen interval.
        
        try {
            $stmt = $db->exec("DELETE FROM experiment WHERE date < DATE_SUB(NOW(), INTERVAL '" . $interval . "' )");
            echo json_encode($stmt);
            ChromePhp::log("Update complete");          //for logging purposes
        } catch (Exception $ex) {
            ChromePhp::log($ex->getMessage());
        }
    }
}
?>