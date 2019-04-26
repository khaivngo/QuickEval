<?php
/**
 * File will get ALL experiments and return them in an array.
 */
require_once('../../db.php');
if($_SESSION['user']['userType'] > 2) {
	return;
}
$userId = $_SESSION['user']['id'];

try {
    $data = array();

    //-----------RANK ORDER---------------------------   
    
    // getting all experiments of type rating
    // and those who are hidden:
    
    $stmt = $db->query(
        "SELECT COUNT(experiment.id) AS total FROM experiment"
        . "Join experimenttype on experiment.experimenttype=experimenttype.id AND experimenttype.type = 'rating'"
        . "Join person On experiment.person=person.id AND person.id = '" . $userId . "'"
    );

    $res = $stmt->fetchAll();

    $data['totalRating'] = $res;  //saving in an array with named index.

    $stmt = $db->query(
        "SELECT COUNT(experiment.id) AS total FROM experiment"
        . "Join experimenttype on experiment.experimenttype=experimenttype.id AND experimenttype.type = \"rating\" AND experiment.isPublic = \"0 = Hidden\"\n"
        . "Join person On experiment.person=person.id AND person.id = '" . $userId . "'"
    );

    $res = $stmt->fetchAll();

    $data['hiddenRating'] = $res;


    //--------------PAIR COMPARISON------------------    

    // getting all experiments of type pair comparison
    // and those who are hidden:
    
    
    $stmt = $db->query(
        "SELECT COUNT(experiment.id) AS total FROM experiment\n"
        . "Join experimenttype on experiment.experimenttype=experimenttype.id AND experimenttype.type = \"pair\"\n"
        . "Join person On experiment.person=person.id AND person.id = '" . $userId . "'"
    );

    $res = $stmt->fetchAll();

    $data['totalPair'] = $res;

    $stmt = $db->query(
        "SELECT COUNT(experiment.id) AS total FROM experiment\n"
        . "Join experimenttype on experiment.experimenttype=experimenttype.id AND experimenttype.type = \"pair\" AND experiment.isPublic = \"0 = Hidden\"\n"
        . "Join person On experiment.person=person.id AND person.id = '" . $userId . "'"
    );

    $res = $stmt->fetchAll();

    $data['hiddenPair'] = $res;


    //-------------------------CATEGORY------------------------------------   

    // getting all experiments of type category
    // and those who are hidden:
    
    
    $stmt = $db->query(
        "SELECT COUNT(experiment.id) AS total FROM experiment\n"
        . "Join experimenttype on experiment.experimenttype=experimenttype.id AND experimenttype.type = \"category\"\n"
        . "Join person On experiment.person=person.id AND person.id = '" . $userId . "'"
    );

    $res = $stmt->fetchAll();

    $data['totalCategory'] = $res;

    $stmt = $db->query(
        "SELECT COUNT(experiment.id) AS total FROM experiment\n"
        . "Join experimenttype on experiment.experimenttype=experimenttype.id AND experimenttype.type = \"category\" AND experiment.isPublic = \"0 = Hidden\"\n"
        . "Join person On experiment.person=person.id AND person.id = '" . $userId . "'"
    );

    $res = $stmt->fetchAll();

    $data['hiddenCategory'] = $res;

    //------------------------------------

    echo json_encode($data);
    exit;
} catch (PDOException $excpt) {
    //
}

?>
