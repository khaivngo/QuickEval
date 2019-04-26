<?php
/**
 * Will register a person to the database. 
 * Will also store workplace for person.
 */
require_once('../../db.php');
require_once('../../ChromePhp.php');

$response = array('Registration successful.');

try {
    $institution;
    $personId;

    //---------------------------REGISTER PERSON---------------------------------//

    $stmt = $db->prepare(
        "INSERT INTO person (firstName, lastName, email, password, age, sex, userType, nationality) " .
        "VALUES(:firstName, :lastName, :email, :password, :age, :sex, '3', :nationality)"
    );

    $stmt->execute(array(
        ':firstName' => $_POST['firstName'],
        ':lastName' => $_POST['lastName'],
        ':email' => strtolower($_POST['email']),
        ':password' => $_POST['password'],
        ':age' => $_POST['age'],
        ':sex' => $_POST['sex'],
        ':nationality' => $_POST['nationality']
    ));

    //---------------------------GET WORKPLACEID---------------------------------//

    $stmt = $db->prepare("SELECT id FROM workplace WHERE name=:institution");
    $stmt->execute(array(':institution' => $_POST['institution']));
    $res = $stmt->fetchAll();
    $institution = $res[0]['id'];

    //---------------------------GET PERSONID---------------------------------//

    $stmt = $db->prepare("SELECT id FROM person WHERE email=:email");
    $stmt->execute(array(':email' => strtolower($_POST['email'])));
    $res = $stmt->fetchAll();
    $personId = $res[0]['id'];

    //-------------------------REGISTER WORKPLACE------------------------------//

    $stmt = $db->prepare(
        "INSERT INTO workplacebelongs (personId, workPlace) " .
        "VALUES(:personId, :workPlace)"
    );

    $stmt->execute(array(
        ':personId' => $personId,
        ':workPlace' => $institution
    ));

    echo json_encode($response);
    exit;
} catch (PDOException $excpt) {
    $response[0] = 'Registration failed, please try again.';
    echo json_encode($response);
    exit;
}

?>