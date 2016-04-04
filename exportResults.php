<?php
header('Content-Type: application/excel');
header('Content-Disposition: attachment; filename="experiment.csv"');

require_once('db.php');
require_once('functions.php');
require_once('ChromePhp.php');

//Gets experimentresults and initializes variables
$id = $_GET['id'];
$ex = getExperimentById($id, $db);
$results;
$images = array();
$experimentOrders = array();
$observers;
$instructions;
$resultRows;


//If no results/user didn't own experiment, redirects to index
if($ex == 0) {
	header('Location: index.php');
	exit;
}

//Gets results for experiment
$results = getExperimentResults($id, $db, ((isset($_GET['complete'])) ? 1 : 0));

//Saves experimentOrders
$experimentOrders = $results[1];

//Stuff which are different on methods
switch($ex['experimentType']) {
	case '1':
	$resultRows = $results[3];
	break;
	case '2':
	$resultRows = $results[3];
	break;
	case '3':
	$resultRows = splitToExperimentOrder(getExperimentRawData($id, $db, 3, (isset($_GET['complete'])) ? 1 : 0), $results['experimentOrders']);

	break;
}

//Add all images to one array
foreach($results[2] as $experimentOrder) {
	$images = array_merge($images, $experimentOrder);
}

//Gets all observers
$sql = "SELECT person.*, usertype.title AS userTypeName, experimentresult.* FROM result  "
. "JOIN person ON result.personId = person.id "
. "JOIN usertype ON person.userType = usertype.id "
. "JOIN experimentresult ON result.experimentId = experimentresult.experiment AND person.id = experimentresult.person "
. "WHERE result.experimentId = ? " .  ((isset($_GET['complete'])) ? ' AND experimentresult.complete  != 1 ' : ' ')
. "GROUP BY person.id ";
$sth = $db->prepare($sql);
$sth->bindParam(1, $id);
$sth->execute();

$observers = $sth->fetchAll();

//Gets all instructions
$sql = "SELECT instruction.* FROM instruction  "
. "JOIN experimentorder ON instruction.id = experimentorder.instruction "
. "JOIN experimentqueue ON experimentorder.experimentQueue = experimentqueue.id "
. "JOIN experiment ON experimentqueue.experiment = experiment.id "
. "WHERE experiment.Id = ? ";

$sth = $db->prepare($sql);
$sth->bindParam(1, $id);
$sth->execute();

$instructions = $sth->fetchAll();

//Gets all input fields
$sql = "SELECT infotype.* FROM experiment"
. " JOIN experimentinfotype ON experiment.id = experimentinfotype.experiment"
. " JOIN infotype ON experimentinfotype.infoType = infotype.id"
. " WHERE experiment.id = ?";
$sth = $db->prepare($sql);
$sth->bindParam(1, $id);
$sth->execute();

$infoTypes = $sth->fetchAll();

//Get all input field results
$sql = "SELECT infofield.* FROM experiment"
. " JOIN experimentinfotype ON experiment.id = experimentinfotype.experiment"
. " JOIN infotype ON experimentinfotype.infoType = infotype.id"
. " JOIN infofield ON infotype.id = infofield.infoType"
. " WHERE experimentinfotype.experiment = ? AND infofield.experiment = ?";
$sth = $db->prepare($sql);
$sth->bindParam(1, $id);
$sth->bindParam(2, $id);
$sth->execute();

$infoTypeResults = $sth->fetchAll();

//METADATA
if(isset($_GET['metadata'])) {
	$data = array('***Metadata***');
	$data[] = 'Name: '.$ex['experimentName'];
	$data[] = 'Description: '.$ex['experimentDescription'];
	$data[] = 'Type of experiment: '.$ex['experimentTypeName'];
	$data[] = 'Timestamp experiment creation: '.$ex['date'];
	$data[] = 'Timestamp export: '. date("Y-m-d H:i:s", time());
	$data[] = 'Scientist: '. $ex['person'];
	$data[] = 'Number of observers: ' . count($observers);
	($ex['experimentType'] == 3) ? $data[] = 'Number of categories: ' . count($results[3]) : '';
	$data[] = 'Number of instructions: ' . count($instructions);
	$data[] = 'Background colour: '. $ex['backgroundColour'];
	$data[] = 'Viewing distance: '. $ex['monitorDistance'];
	$data[] = 'White point: '. $ex['whitePoint'];
	$data[] = 'Screen luminance: '. $ex['screenLuminance'];
	$data[] = 'Ambient illumination: '. $ex['ambientIllumination'];
}

//PARAMETERS
if(isset($_GET['parameters'])) {
	$data[] = PHP_EOL.'***Parameters***';
	$data[] = 'Allow colour deficiency: '. (($ex['allowColourBlind'] == 1) ? 'YES' : 'NO');
	$data[] = 'Display timer: '. (($ex['timer'] == 1) ? 'YES' : 'NO');
	$data[] = 'Display original: '. (($ex['showOriginal'] == 1) ? 'YES' : 'NO');
	$data[] = 'Hidden experiment: '. (($ex['isPublic'] == '1 = Public') ? 'YES' : 'NO');

	//PAIRING PARAMETERS
	if($ex['experimentType'] == 2) {
		$data[] = 'Forced choice: '. (($ex['allowTies'] == 0) ? 'YES' : 'NO');
		$data[] = 'Same pair twice (flipped): '. (($ex['samePair'] == 1) ? 'YES' : 'NO');
	}
}

//CATEGORIES IF METHOD CATEGORY
if($ex['experimentType'] == 3) {
	$data[] = PHP_EOL.'***Categories***';
	for($i = 0; $i < count($results[3]); $i++) {
		$data[] = 'Category' . ($i+1) . ',' . $results[3][$i]['name'];
	}
}

//INSTRUCTIONS
if(isset($_GET['instructions'])) {
	$data[] = PHP_EOL.'***Instructions***';
	for($i = 0; $i < count($instructions); $i++) {
		$data[] = 'Instruction' . ($i+1) . ',' . $instructions[$i]['text'];
	}
}

//IMAGE SETS
if(isset($_GET['image-sets'])) {
	$data[] = PHP_EOL.'***Image Set***';
	for($i = 0; $i < count($experimentOrders); $i++) {
		$row = 'Imageset' . ($i+1) . ',' . $experimentOrders[$i]['name'];
		foreach($results[2][$i] as $image) {
			$row = $row . ',' . $image['name'];
		}
		$data[] = $row;
	}
}

//OBSERVER DATA
if(isset($_GET['observer-data'])) {
	$data[] = PHP_EOL.'***Observer data***';
	for($i = 0; $i < count($observers); $i++) {
		$o = $observers[$i];
		$data[] = 'Observer' . ($i+1) . ',' . $o['firstName'] . ' ' . $o['lastName'] . ',' .
		$o['email'] . ',' . $o['nationality'] . ',' . $o['sex'] . ',' . $o['age'] . ',' .
		$o['colourBlindFlag'] . ',' . $o['userTypeName'] . ',' . $o['os'] . ',' . $o['xDimension'] . ',' .
		$o['yDimension'] . ',' . $o['startTime'] . ',' . $o['endTime'];
	}
}

//INPUT FIELDS
if(isset($_GET['input-field-data'])) {
	$data[] = PHP_EOL.'***Input fields***';

	for($i = 0; $i < count($infoTypes); $i++) {
		$t = $infoTypes[$i];
		$data[] = 'InputField' . ($i + 1) . ',' . $t['info'];
	}

	//INPUT FIELD RESULTS
	$data[] = PHP_EOL.'***Input field results***';
	for($i = 0; $i < count($infoTypeResults); $i++) {
		$t = $infoTypeResults[$i];
		$data[] = 'InputField' . (arrayObjectIndexOf($infoTypes, $t['infoType'], 'id') + 1) . ',' .
		'Observer' . (arrayObjectIndexOf($observers, $t['person'], 'person') + 1) . ',' . $t['text'];
	}
}

//RESULTS
if(isset($_GET['results'])) {
	$data[] = PHP_EOL.'***Experiment results***';

	//If rank order
	if($ex['experimentType'] == 1) {
		foreach($resultRows as $experimentOrder) {
			foreach($experimentOrder as $result) {
				$row = 'Imageset' . (arrayObjectIndexOf($results['experimentOrders'], $result['eOrderId'], 'eOrder') + 1) . ',' .
					$result['person'] . ',' . $result['timeStamp'];

				for($j = 0; $j < count($result) - 3; $j++) {
					$row = $row . ',' . $result[$j];
				}

				$data[] = $row;
			}
		}
	} elseif($ex['experimentType'] == 2) {	//IF PAIRING

		foreach($resultRows as $experimentOrder) {
			for($i = 0; $i < count($experimentOrder); $i++) {
				$result = $experimentOrder[$i];
				$data[] = 'Imageset' . (arrayObjectIndexOf($results['experimentOrders'], $result['eOrderId'], 'eOrder') + 1) . ',' .
				'Observer' . (arrayObjectIndexOf($observers, $result['personId'], 'person') + 1) . ',' .
				$result['created'] . ',' . $result['name'] . ',' .
				$result['wonAgainstName'] . ',' . (($result['chooseNone'] == null) ? ($result['name']) : '0');

			}
		}

	} elseif($ex['experimentType'] == 3) {	//IF CATEGORY

		foreach($resultRows as $experimentOrder) {
			foreach($experimentOrder as $result) {
				$data[] = ('Imageset' . (arrayObjectIndexOf($results['experimentOrders'], $result['experimentOrder'], 'eOrder') + 1)) . ',' .
				'Observer' . (arrayObjectIndexOf($observers, $result['personId'], 'person') + 1) . ',' . $result['created'] . ',' . $result['name'] . ',' .
				'Category' . (arrayObjectIndexOf($results[3], $result['category'], 'category') + 1);
			}
		}
	}
}

$data = str_replace('"', '', $data);

//Prints to file
$fp = fopen('php://output', 'w');
foreach( $data as $line) {
	fwrite($fp,$line.PHP_EOL);
}

//Closes file
fclose($fp);

/**
 * Splits results to array based on experimentorder
 * @param  array $array            array with results
 * @param  array $experimentOrders array with experimentorders
 * @return array                   array with results indexed by experimentorders
 */
function splitToExperimentOrder($array, $experimentOrders) {
	$experimentOrderArrays = array();
	foreach($experimentOrders as $experimentOrder) {
		$experimentOrderRow = array();
		$id = $experimentOrder['eOrder'];

		foreach($array as $result) {
			if($result['experimentOrder'] == $id) {
				$experimentOrderRow[] = $result;
			}
		}
		$experimentOrderArrays[] = $experimentOrderRow;
	}
	return $experimentOrderArrays;
}

?>
