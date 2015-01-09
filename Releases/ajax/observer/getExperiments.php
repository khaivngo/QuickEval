<?php

require_once('../../db.php');
require_once('../../ChromePhp.php');

try {

    $experiment = "";
    $institute = $_POST['institute'];
    $organization = $_POST['organization'];
    $scientist = $_POST['scientist'];

    if (isset($_POST['experiment'])) {    //Experiment-title is only set if user used search-field
        $experiment = $_POST['experiment'];
    }

    //Setting up core query
    $query = "SELECT e.* FROM experiment e "; //First part of query to join tables
   
    //Adds person to query
    $query = $query . " JOIN person p ON p.id = e.person ";
    
    //Setting up subquery to include last part of query; wheres, order by
    $subQuery = " WHERE ((CONCAT(LOWER(p.firstName), LOWER(p.lastName)) LIKE :scientist) OR "
            . " (CONCAT(LOWER(p.lastName), LOWER(p.firstName)) LIKE :scientist)) ";


    //Adds institute to query if institute is set
    if ($institute != "") {
        $query = $query . " JOIN workplacebelongs wb ON p.id = wb.personId "
                . " JOIN workplace w1 ON wb.workPlace = w1.id ";
        $subQuery = $subQuery . " AND LOWER(w1.name) LIKE :institute ";
    }

    //Adds organization to query if organization is set
    if ($organization != "") {
        $query = $query . " JOIN workplacebelongs wb ON p.id = wb.personId "
                . " JOIN workplace w2 ON wb.workPlace = w2.id ";
        $subQuery = $subQuery . " AND LOWER(w2.name) LIKE :organization ";
    }

    //Ensures results are public only
    $subQuery = $subQuery . " AND e.isPublic = 1 ";

    //If experiment name is available
    if (isset($_POST['experiment'])) {

        //Adds experiment-title to the query
        $subQuery = $subQuery . " AND LOWER(e.title) LIKE :experiment ";
    } else {

        //If experiment-name is not sent
        $subQuery = $subQuery . " ORDER BY title ASC, RAND() ";
    }

    
    ChromePhp::log($query.$subQuery);
    //Sets up query and prepares
    $stmt = $db->prepare($query . $subQuery);

    //Binds scientist if set
    if ($scientist != "") {
        $stmt->bindValue(':scientist', '%' . str_replace(array(' ', ','), '%', strtolower($scientist)) . '%');
    } else {
        $stmt->bindValue(':scientist', '%');
    }

    //Binds institute if set
    if ($institute != "") {
        $stmt->bindValue(':institute', '%' . strtolower($institute) . '%');
    }

    //Binds organization if set
    if ($organization != "") {
        $stmt->bindValue(':organization', '%' . strtolower($organization) . '%');
    }

    //Binds experiment if set
    if (isset($_POST['experiment'])) {
        $stmt->bindValue(':experiment', '%' . strtolower($experiment) . '%');
    }

    $stmt->execute();
    $rows = $stmt->rowCount();

    if ($rows == 0) {
        $res = 0;
    } else {
        $res = $stmt->fetchAll();
    }


    ChromePhp::log($res);
    echo json_encode($res);
} catch (PDOException $excpt) {
    ChromePhp::log($excpt->getMessage());
}
?>