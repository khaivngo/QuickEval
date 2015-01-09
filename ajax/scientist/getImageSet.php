<?php
/**
 * Will get given pictureset for a person.
 */
require_once('../../db.php');

    $imageSet = $_POST['imageSet'];
    $scientist = $_SESSION['user']['id'];

    $stmt = $db->prepare("SELECT * FROM pictureset " .
            "WHERE id = :id AND person = :scientist");
  
    $stmt->execute(array(':id' => $imageSet, ':scientist' => $scientist));
       
    $res = $stmt->fetch();

    echo json_encode($res);

?>


