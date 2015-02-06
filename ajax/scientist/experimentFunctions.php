<?php
/**
 * This php file will handle most of the experimentFunctions regarding creating a new experiment.
 *
 */


require_once('../../db.php');
if ($_SESSION['user']['userType'] > 2) {
    return;
}


$option = $_GET['option'];


    if ($option == "newExperiment") {
        try {
            $db->beginTransaction();
            $sql = "INSERT INTO `experiment`
		(`id`, `shortDescription`, `longDescription`, `title`, `date`,
		 `isPublic`, `allowColourBlind`, `backgroundColour`,
		 `allowTies`, `showOriginal`, `horizontalFlip`,
		 `monitorDistance`, `lightType`, `naturalLighting`, `screenLuminance`,
		 `whitePoint`, `whitePointRoom`, `ambientIllumination`, `person`,
	     `experimentType`, `inviteHash`) 
		     


	   VALUES
	   (NULL, ?, ?, ?, CURRENT_TIMESTAMP,
		      '0 = Hidden', '0', '#808080',
		      '1', '1', '1',
		      '1', NULL, NULL,?,
		      ?, ?, ?, ?,
		      ?, ?);";


            $screenLuminance = null;
            $screenWhitePoint = null;
            $roomIllumination = null;
            $ambientIllumination = null;

            $time = time();
            $md5 = md5($time);
            $inviteHash = substr($md5, 0, 10);


            if (isset($_GET['screenLuminance'])) {
                $screenLuminance = $_GET['screenLuminance'];
            }
            if (isset($_GET['screenWhitePoint'])) {
                $screenWhitePoint = $_GET['screenWhitePoint'];
            }
            if (isset($_GET['roomIllumination'])) {
                $roomIllumination = $_GET['roomIllumination'];
            }
            if (isset($_GET['ambientIllumination'])) {
                $ambientIllumination = $_GET['ambientIllumination'];
            }



            $sth = $db->prepare($sql);
            $sth->bindParam(1, $_GET['shortDescription']);
            $sth->bindParam(2, $_GET['description']);
            $sth->bindParam(3, $_GET['name']);

            $sth->bindParam(4, $screenLuminance);
            $sth->bindParam(5, $screenWhitePoint);
            $sth->bindParam(6, $roomIllumination);
            $sth->bindParam(7, $ambientIllumination);

            $sth->bindParam(8, $_SESSION['user']['id']);
            $sth->bindParam(9, $_GET['exType']);
            $sth->bindParam(10, $inviteHash);
            $sth->execute();
            $sql = "SELECT * FROM experiment WHERE person = ? ORDER BY id DESC LIMIT 1;";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $_SESSION['user']['id']);
            $sth->execute();
            $result = $sth->fetch();

            //This is actually not needed
            $sql = "INSERT INTO `experimentqueue` (`experiment`) VALUES (?);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $result['id']);
            $sth->execute();

            $_SESSION['user']['activeExperiment'] = $result['id'];
            echo json_encode($result['id']);

            $db->commit();

        } catch (PDOException $excpt) {
            echo $excpt;
            echo json_encode(0);
            $db->rollBack();
        }
    } else if ($option == "addParametersPair") {
        $experimentId = $_GET['experimentId'];
        $forcedPick = $_GET['forcedPick'];
        $samePairTwice = $_GET['samePairTwice'];
        $showOriginal = $_GET['showOriginal'];
        $allowColorblind = $_GET['colorblind'];
        $hidden = $_GET['hidden'];
        $timer = $_GET['timer'];
        $backColour = $_GET['backgroundColour'];


        $sql = "UPDATE  `experiment` SET
        `isPublic`         =  ?,
		`allowColourBlind` =  ?,
		`backgroundColour` =  ?,
		`allowTies`        =  ?,
		`showOriginal`     =  ?,
		`samePair`         =  ?,
		`timer`            =  ?
		WHERE  `experiment`.`id` = ?;";

        $sth = $db->prepare($sql);
        $sth->bindParam(1, $hidden);
        $sth->bindParam(2, $allowColorblind);
        $sth->bindParam(3, $backColour);
        $sth->bindParam(4, $forcedPick);
        $sth->bindParam(5, $showOriginal);
        $sth->bindParam(6, $samePairTwice);
        $sth->bindParam(7, $timer);
        $sth->bindParam(8, $experimentId);
        $sth->execute();


    } else if ($option == "getExperimentId") {
        $experimentId;
        if (isset($_SESSION['user']['activeExperiment'])) {
            $experimentId = $_SESSION['user']['activeExperiment'];
        } else {
            $sql = "SELECT * FROM experiment WHERE person = ? ORDER BY id DESC LIMIT 1;";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $_SESSION['user']['id']);
            $sth->execute();
            $result = $sth->fetch();
            $_SESSION['user']['activeExperiment'] = $result['id'];
            $experimentId = $result['id'];
        }

        echo json_encode($experimentId);
    } else if ($option == "addObserverInputFields") {
        try {
            $db->beginTransaction();
            //	$sex = 			$_GET['sex'];
            //  $age = $_GET['age'];
            //  $nationality = $_GET['nationality'];

            if (isset($_GET['defaultFields'])) {
                $defaultFields = $_GET['defaultFields'];

                foreach ($defaultFields as $fieldId) {
                    $sql = "INSERT INTO experimentinfotype (experiment, infoType) VALUES (?, ?);";
                    $sth = $db->prepare($sql);
                    $sth->bindParam(1, $_SESSION['user']['activeExperiment']);
                    $sth->bindParam(2, $fieldId);
                    $sth->execute();
                }
            }
            if (isset($_GET['customFields'])) {
                $customFields = $_GET['customFields'];
                foreach ($customFields as $customField) {

                    $sql = "INSERT INTO `infotype` (`id`, `standardFlag`, `info`, `person`) VALUES (NULL, 0, :info, :person);";
                    $sth = $db->prepare($sql);
                    $sth->bindParam(':info', $customField);
                    $sth->bindParam(':person', $_SESSION['user']['id']);
                    $sth->execute();
                    $newInfotypeId = $db->lastInsertId();

                    $sql = "INSERT INTO `experimentinfotype` (`id`, `experiment`, `infoType`) VALUES (NULL, :experiment, :infotype);";
                    $sth = $db->prepare($sql);
                    $sth->bindParam(':experiment', $_SESSION['user']['activeExperiment']);
                    $sth->bindParam(':infotype', $newInfotypeId);
                    $sth->execute();
                }
            }

            $db->commit();

        } catch (PDOException $excpt) {
            echo json_encode(0);
            //$db->rollBack();
        }

    } else if ($option == "addPictureQueueOrInstruction") {
        try {
            $db->beginTransaction();
            $experimentId = $_GET['experimentId'];
            //Gets experimentQueue for experiment
            $sql = "SELECT id FROM experimentqueue WHERE experiment = ?;";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $experimentId);
            $sth->execute();
            $result = $sth->fetch();
            $experimentQueueId = $result['id'];

            $pictureQueueId = null;
            $experimentInstructionId = null;

            if (isset($_GET['pictureQueueId'])) {
                $pictureQueueId = $_GET['pictureQueueId'];
            } else if (isset($_GET['experimentInstructionId'])) {
                $experimentInstructionId = $_GET['experimentInstructionId'];
            }

            $sql = "INSERT INTO `experimentorder`
		(`pictureSet`, `experimentQueue`, `pictureQueue`, `instruction`)
		 VALUES (NULL, ?, ?, ?);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $experimentQueueId);
            $sth->bindParam(2, $pictureQueueId);
            $sth->bindParam(3, $experimentInstructionId);
            $sth->execute();
            $db->commit();

        } catch (PDOException $excpt) {
            echo json_encode(0);
            echo $excpt;
            $db->rollBack();
        }
    }
//link for testing purposes.
//http://localhost/quickeval/QuickEval/ajax/scientist/experimentFunctions.php?option=newExperiment&shortDescription=Descriptionyo&name=navn&description=longdescription&exType=1

?>