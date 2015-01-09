<?php
/**
 * Will get total steps for a given experiments.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

define('res',"");

try {                                   //gets whether the experiment is to show original
    $stmt = $db->prepare("select experimentOrder.pictureQueue FROM experimentqueue, experimentOrder WHERE experimentqueue.experiment = :id ".  
							"AND experimentorder.experimentQueue = experimentqueue.id AND experimentorder.pictureQueue IS NOT NULL");

    $stmt->execute(array(':id' => $_POST['experimentId']));
	$test = $stmt->fetchAll();
	
	$res = $test;	
	
} catch (Exception $excpt) {
  // ChromePhp::log($excpt->getMessage());
}

try	{

	$stmt = $db->prepare("Select MAX(pOrder) FROM pictureorder WHERE pictureQueue = '" . $res[0]['pictureQueue'] . "'");
	$stmt->execute();
	$res = $stmt->fetchAll();
	
	echo json_encode($res);

} catch (Exception $excpt) {
   //ChromePhp::log($excpt->getMessage());
}






	

?>