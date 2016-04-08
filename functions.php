<?php

/**
 * General functions to use between files. This file does nothing on its own except including
 * db.php.
 */

include_once('db.php');
require_once('ChromePhp.php');
/**
 * Forces a session update for user and returns the users userType
 * @return null If there is an error, or userType if not
 */
function checkLogin($db) {
	try {
        if (isset($_SESSION['user'])) {     //Checks for existing session

        	$stmt = $db->prepare("SELECT * FROM person WHERE id=:id");

            $stmt->execute(array(':id' => $_SESSION['user']['id']));    //Gets userdata
        } else {        //If user isn't in database
        return null;
    }

    $rows = $stmt->rowCount();

        if ($rows == 1) {                   //Checks if found user
        	$res = $stmt->fetchAll();
            $_SESSION['user'] = $res[0];    //Updates session
        } else {                            //If couldn't find user
        return null;
    }

        return $_SESSION['user']['userType'];   //Returns userlevel

    } catch (PDOException $excpt) {
        return null;    //If SQL error
    }
}

/**
 * Redirects from an experiment to login, and after logging in the user
 * will be takes to the correct experiment
 * @param  string $url URL of *experiment.php file, containing the hash as GET data. For example "ratingexperiment.php?hash=asdfginoadfsnio"
 */
function redirectAfterLogin($url) {
	header('Location: login.php?redirect=' . $url);
	exit;
}

/**
 * The purpose of this function is to simplify the syntax a bit.
 * And it also makes sure we never forget to call exit;
 */
function redirect($page) {
	header("Location: " . $page);
	exit;
}

function checkOwnerForPicture($pictures, $personId, $db) {
	foreach($pictures as $pictureId) {
		$sql = "SELECT * FROM picture
		JOIN pictureSet
		ON picture.pictureSet=pictureSet.id
		WHERE picture.id = ?;";
		$sth = $db->prepare($sql);
		$sth->bindParam(1,$pictureId);
		$sth->execute();

		$result = $sth->fetch();
		if($personId != $result['person']) {
			return false;
		}
	}
	return true;

}

function generateUrl($picture){
	$index = strripos($picture[1],".");
	$fileType = substr($picture[1],$index, strlen($picture[1]));
	$url = array();
	$url['url'] = "uploads/" . $picture['person'] . "/" . $picture[4] . "/" . $picture['url'] . $fileType;
	$url['id'] = $picture[0];
	return $url;
}
function getUrlForPicture($pictureId, $db) {
	$sql = "SELECT * FROM picture
	JOIN pictureset ON picture.pictureSet = pictureset.id
	WHERE picture.id = ?;";
	$sth = $db->prepare($sql);
	$sth->bindParam(1,$pictureId['pictureId']);
	$sth->execute();
	$row = $sth->fetch();
	$url = generateUrl($row);

	/*var_dump($_SESSION['activeObserverExperiment']);
	if(isset($_SESSION['activeObserverExperiment'])) {	//This will get the current active pictureOrder which are done in experiment
		foreach($_SESSION['activeObserverExperiment']['activePictureOrder'] as $index) {
			if($_SESSION['activeObserverExperiment']['pictureOrder'][$index]['picture'] == $pictureId) {
				$url['pictureOrderId'] = $_SESSION['activeObserverExperiment']['pictureOrder'][$index][5]; //Number 5 is the id for the pictureOrder
			}
		}
	}*/
	$url['pictureOrderId'] = $pictureId['pictureOrderId'];

	$sql = "SELECT * FROM picture
	JOIN pictureset ON picture.pictureset=pictureset.id
	WHERE picture.isOriginal = 1 AND picture.pictureSet = ?;";

	$sth = $db->prepare($sql);
	$sth->bindParam(1, $row['pictureSet']);
	$sth->execute();
	$result = $sth->fetch();
	$url['originalUrl'] = generateUrl($result);
	return $url;
}

function getExperimentById($id, $db) {


	$sql = "SELECT *, experimenttype.name AS experimentTypeName, experiment.title AS experimentName, experiment.longDescription AS experimentDescription "
	. " FROM experiment "
	. " JOIN experimenttype ON experiment.experimentType = experimenttype.id "
	. " WHERE person = ? AND experiment.id = ?";

	$sth = $db->prepare($sql);
	$sth->bindParam(1,$_SESSION['user']['id']);
	$sth->bindParam(2,$id);
	$sth->execute();
	$result = $sth->fetch();


	return $result;
}


if(isset($_GET['option'])) {
	$option = $_GET['option'];
	if($option == "getPictureUrl") {
		$pictureId = $_GET['pictureId'];
		echo json_encode(getUrlForPicture($pictureId, $db));
	}
}

/**
 * Returns results for category experiments
 * @param  int $experimentId Id of experiment
 * @param  object $db           Object of current database
 * @param  int $type         experimenttype id
 * @return array               Array of rows with result data
 */
function getExperimentRawData($experimentId, $db, $type, $complete) {

	$result = 0;

	if($type == 3) {

		//Magic be here, DO NOT TOUCH OR GOD BE WITH YOU
		$sql = "SELECT result.*, picture.*, experimentorder.eOrder as experimentOrder, person.firstName, person.lastName, categoryname.name AS categoryName"
		. " FROM `result` "
		. " JOIN pictureorder ON result.pictureOrderId = pictureorder.id"
		. " JOIN picture ON pictureorder.picture = picture.id"
		. " JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id"
		. " JOIN experimentorder ON picturequeue.id = experimentorder.pictureQueue"
		. " JOIN person ON result.personId = person.id"
		. " JOIN categoryname ON result.category = categoryname.id"
		. (($complete == 1) ?  " JOIN experimentresult ON result.experimentId = experimentresult.experiment " : '')
		. " WHERE result.experimentId = ? ". (($complete == 1) ? "AND experimentresult.complete != 1 AND experimentresult.person = person.id " : "");
		$sth = $db->prepare($sql);
		$sth->bindParam(1, $experimentId);
		$sth->execute();

		$result = $sth->fetchAll();
	}

	return $result;
}

/**
 * Returns data of experiment and resultsbased on experimenttype
 * @param  int $experimentId Id of experiment
 * @param  object $db           object of current database
 * @return array               [0] = experimenttype, [1] = image sets, [2] = experimentorders, [3] = results
 */
function getExperimentResults($experimentId, $db, $complete) {
	$result = array();
	$imageSetImages = array();
	$pictureQueues = array();

    //Gets experiment type and adds to result array
	$sql = "SELECT experimentType FROM experiment WHERE experiment.id = ?";
	$sth = $db->prepare($sql);
	$sth->bindParam(1, $experimentId);
	$sth->execute();

	$type = $sth->fetch();
	$type = $type['experimentType'];

	$result[] = $type;

    //Gets all imageset id's and adds to result
	$sql = "SELECT pictureset.id, pictureset.name FROM experiment "
	. " JOIN experimentqueue ON experimentqueue.experiment = experiment.id "
	. " JOIN experimentorder ON experimentqueue.id = experimentorder.experimentQueue "
	. " JOIN picturequeue ON experimentorder.pictureQueue = picturequeue.id  "
	. " JOIN pictureorder ON picturequeue.id = pictureorder.pictureQueue  "
	. " JOIN picture ON pictureorder.picture = picture.id  "
	. " JOIN pictureset ON picture.pictureSet = pictureset.id  "
	. " WHERE experiment.id = ? AND experiment.person = ?  "
	. " GROUP BY eOrder";

	$sth = $db->prepare($sql);
	$sth->bindParam(1, $experimentId);
	$sth->bindParam(2, $_SESSION['user']['id']);
	$sth->execute();
	$imageSets = $sth->fetchAll();
	$result[] = $imageSets;

    //Gets all experimentorders, since not all images in imagesets might be used in experiment
	$sql = "SELECT experimentorder.eOrder FROM experiment "
	. " JOIN experimentqueue ON experimentqueue.experiment = experiment.id "
	. " JOIN experimentorder ON experimentqueue.id = experimentorder.experimentQueue "
	. " JOIN picturequeue ON experimentorder.pictureQueue = picturequeue.id  "
	. " JOIN pictureorder ON picturequeue.id = pictureorder.pictureQueue  "
	. " JOIN picture ON pictureorder.picture = picture.id  "
	. " JOIN pictureset ON picture.pictureSet = pictureset.id  "
	. " WHERE experiment.id = ? AND experiment.person = ?  "
	. " GROUP BY pictureorder.pictureQueue";

	$sth = $db->prepare($sql);
	$sth->bindParam(1, $experimentId);
	$sth->bindParam(2, $_SESSION['user']['id']);
	$sth->execute();
	$experimentOrders = $sth->fetchAll();

	$result['experimentOrders'] = $experimentOrders;

	foreach ($experimentOrders as $experimentOrder) {
		$sql = "SELECT picture.name, picture.id, picture.pictureset FROM picture "


		. " JOIN pictureorder ON picture.id = pictureorder.picture "
        . " JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id "
		. " JOIN experimentorder ON experimentorder.picturequeue = picturequeue.id "
		. " JOIN experimentqueue ON experimentorder.experimentQueue = experimentqueue.id "
        . " JOIN experiment ON experimentqueue.experiment = experiment.id "
		. " WHERE experiment.id = ? AND experiment.person = ? AND experimentorder.eOrder = ?"
		. " GROUP BY picture.id";

		$sth = $db->prepare($sql);
		$sth->bindParam(1, $experimentId);
		$sth->bindParam(2, $_SESSION['user']['id']);
		$sth->bindParam(3, $experimentOrder['eOrder']);
		$sth->execute();
		$imageSetImages[] = $sth->fetchAll();
	}

	$result[] = $imageSetImages;

    // Retrieves url for thumbnail picture of each image set
    foreach($imageSets as $key=>$imageSet) {
        $sql = "SELECT * FROM picture
	    JOIN pictureset ON picture.pictureset=pictureset.id
	    WHERE picture.isOriginal = 1 AND picture.pictureSet = ?;";
        $sth = $db->prepare($sql);
        $sth->bindParam(1, $imageSet['id']);
        $sth->execute();
        $result['imageUrl'][$key] = generateUrl($sth->fetch());
    }

    //If rating experiment
	if ($type == 1) {

		$resultArray = array();

        //Iterates through all experimentorders
		for ($i = 0; $i < sizeof($experimentOrders); $i++) {
            $currentResult = array(); //Result for current experimentorder
            $tempArray = array();
            $experimentOrder = $experimentOrders[$i];
            $imagesLength = sizeof($imageSetImages[$i]);


            $sql = "SELECT result.*, person.firstName, person.lastName, picture.id as pictureId, picture.name, result.created, pictureset.id AS pictureSet, eOrder "
            . "FROM `result` \n"
            . "JOIN experiment ON result.experimentId = experiment.id\n"
            . "JOIN person ON result.personId = person.id\n"
            . "JOIN pictureorder ON result.pictureOrderId = pictureorder.id\n"
            . "JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id\n"
            . "JOIN experimentorder ON picturequeue.id = experimentorder.pictureQueue\n"
            . "JOIN picture ON picture.id = pictureorder.picture \n"
            . "JOIN pictureset ON picture.pictureSet = pictureset.id \n"
            . (($complete == 1) ?  " JOIN experimentresult ON result.experimentId = experimentresult.experiment AND experimentresult.person = result.personId " : ' ')
            . "WHERE result.experimentId = ? AND experimentorder.eOrder = ? AND experiment.person = ? ". (($complete == 1) ? " AND experimentresult.complete != 1 " : " ")
            . "ORDER BY result.id ";

            $sth = $db->prepare($sql);
            $sth->bindParam(1, $experimentId);
            $sth->bindParam(2, $experimentOrder['eOrder']);
            $sth->bindParam(3, $_SESSION['user']['id']);
            $sth->execute();

            $experimentOrderResult = $sth->fetchAll();
            $rows = sizeof($experimentOrderResult);



            //Iterates through first row of all results
            for ($k = 0; $k < $rows; $k+=$imagesLength) {
            	$tempArray = array();

                //Iterates through rest of results
            	for ($j = $k; $j < $k + $imagesLength; $j++) {

            		$tmp = $experimentOrderResult[$j];

            		$tempArray['person'] = $tmp['firstName'] . ' ' . $tmp['lastName'];
            		$tempArray['timeStamp'] = $tmp['created'];
            		$tempArray['eOrderId'] = $tmp['eOrder'];
            		$imageIndex = arrayObjectIndexOf($imageSetImages[$i], $tmp['pictureId'], 'id');

            		$tempArray[$j - $k] = $imageIndex + 1;

                    // $resultRow = array('pictureName' 	=> $tmp['name'],
                    //                    'person' 		=> ($tmp['lastName'].", ".$tmp['firstName']),
                    //                    'pictureId' 		=> $tmp['pictureId']);
                    // $currentResult[] = $resultRow;
            	}

                //Saves current row to experimentorderarray
            	$currentResult[] = $tempArray;

            }

            //Saves current experimentorderresult to array
            $resultArray[] = $currentResult;
        }
        //Adds all results to array
        $result[] = $resultArray;

    } elseif ($type == 2) {	//If paired comparison

    	$pairResults = array();

    	foreach ($experimentOrders as $experimentOrder) {

    		$sql = "SELECT picture.name, picture.id AS pictureId, pictureorder.id AS orderId, pictureorder.pOrder po, "
    		. "COUNT(result.pictureOrderId) AS won, result.chooseNone, experimentorder.eOrder AS eOrderId, result.personId, result.created, "
    		. "(SELECT picture.id "
    		    . " FROM picture "
    		    . " JOIN pictureorder ON picture.id = pictureorder.picture "
    		    . " JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id "
    		    . " JOIN experimentorder ON picturequeue.id = experimentorder.pictureQueue "
    		    . " WHERE pictureorder.pOrder = po AND experimentorder.eOrder = eOrderId AND picture.id != pictureId "
    		    . " LIMIT 0,1) AS wonAgainst,  "
			. "(SELECT picture.name "
			    . " FROM picture "
			    . " JOIN pictureorder ON picture.id = pictureorder.picture "
			    . " JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id "
			    . " JOIN experimentorder ON picturequeue.id = experimentorder.pictureQueue "
			    . " WHERE pictureorder.pOrder = po AND experimentorder.eOrder = eOrderId AND picture.id != pictureId "
			    . " LIMIT 0,1) AS wonAgainstName  "
			. "FROM pictureorder  "
			. "JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id  "
			. "JOIN experimentorder ON picturequeue.id = experimentorder.pictureQueue  "
			. "JOIN experimentqueue on experimentorder.experimentQueue = experimentqueue.id  "
			. "JOIN experiment ON experiment.id = experimentqueue.experiment  "
			. "JOIN picture ON pictureorder.picture = picture.id  "
			. "LEFT JOIN result ON pictureorder.id = result.pictureOrderId  "
			. (($complete == 1) ?  " JOIN experimentresult ON result.experimentId = experimentresult.experiment AND experimentresult.person = result.personId " : ' ')
			. "WHERE experiment.id = ? AND experiment.person = ? AND experimentorder.eOrder = ? AND created IS NOT NULL ". (($complete == 1) ? " AND experimentresult.complete != 1 " : " ")
			. "GROUP BY orderId, personId "
			. "ORDER BY orderId ";

			$sth = $db->prepare($sql);
			$sth->bindParam(1, $experimentId);
			$sth->bindParam(2, $_SESSION['user']['id']);
			$sth->bindParam(3, $experimentOrder['eOrder']);
			$sth->execute();
			$pairResults[] = $sth->fetchAll();
			}


			$result[] = $pairResults;

			        //If category experiment
		} elseif ($type == 3) {

        	//Gets all categories and saves in result, needed in case all categories aren't chosen
			$sql = "SELECT categoryname.name, categoryname.id FROM categoryname "
			. " JOIN experimentcategory ON categoryname.id = experimentcategory.category "
			. " JOIN experiment ON experiment.id = experimentcategory.experiment "
			. " WHERE experiment.id = ? AND experiment.person = ?";

			$sth = $db->prepare($sql);
			$sth->bindParam(1, $experimentId);
			$sth->bindParam(2, $_SESSION['user']['id']);
			$sth->execute();

			$result[] = $sth->fetchAll();

		        //Gets all pictures and saves in result, needed in case all pictures aren't reviewed yet
			$sql = "SELECT picture.name, picture.id, picture.pictureset FROM result "
			. " JOIN experiment ON experiment.id = result.experimentId "
			. " JOIN pictureorder ON result.pictureOrderId = pictureorder.id "
			. " JOIN picture ON pictureorder.picture = picture.id "
			. " WHERE experiment.id = ? AND experiment.person = ? "
			. " GROUP BY picture.id";

			$sth = $db->prepare($sql);
			$sth->bindParam(1, $experimentId);
			$sth->bindParam(2, $_SESSION['user']['id']);
			$sth->execute();

			$result[] = $sth->fetchAll();

			$resultArray = array();

			for ($i = 0; $i < sizeof($experimentOrders); $i++) {
		        $currentResult = array(); //Result for current experimentorder
		        $tempArray = array();
		        $experimentOrder = $experimentOrders[$i];
		        $imagesLength = sizeof($imageSetImages[$i]);

		    	//Gets all results and saves in result
				$sql = "SELECT categoryname.name AS categoryName, result.category AS categoryId, experiment.id AS experimentId, "
				. " experiment.title AS experimentName,COUNT(categoryname.name) AS points, picture.name AS pictureName, "
				. " picture.url, picture.pictureSet, picture.id as pictureId "
				. " FROM `result` "
				. " JOIN experiment ON result.experimentId = experiment.id "
				. " JOIN experimenttype ON experiment.experimentType = experimenttype.id "
				. " JOIN categoryname ON result.category = categoryname.id "
				. " JOIN pictureorder ON pictureorder.id = result.pictureOrderId "
				. " JOIN picturequeue ON pictureorder.pictureQueue = picturequeue.id "
				. " JOIN experimentorder ON picturequeue.id = experimentorder.pictureQueue "
				. " JOIN picture ON pictureorder.picture = picture.id "
				. (($complete == 1) ?  " JOIN experimentresult ON result.experimentId = experimentresult.id AND experimentresult.person = result.personId " : ' ')
				. " WHERE experimenttype.id = 3 AND experiment.id = ? AND experiment.person = ? AND experimentorder.eOrder = ? "
				. (($complete == 1) ? " AND experimentresult.complete != 1" : " ")
				. " GROUP BY picture.id, result.category ";

				$sth = $db->prepare($sql);
				$sth->bindParam(1, $experimentId);
				$sth->bindParam(2, $_SESSION['user']['id']);
				$sth->bindParam(3, $experimentOrder['eOrder']);
				$sth->execute();
				$resultArray[] = $sth->fetchAll();
			}

			$result[] = $resultArray;

		    //If error
		} else {
			$result = 0;
		}

	return $result;
}

function arrayObjectIndexOf($myArray, $searchTerm, $property) {

	foreach ($myArray as $key => $subarray) {
		if (in_array($searchTerm, $subarray)) {
			return $key;
		}
	}
	return -1;
}

?>
