<?php
require_once('../../db.php');
require_once('../../ChromePhp.php');


    $imageSet = $_POST['imageSet'];
    $scientist = $_SESSION['user']['id'];

    $stmt = $db->prepare("SELECT * FROM pictureset " .
            "WHERE id = :id AND person = :scientist");
  
    $stmt->execute(array(':id' => $imageSet, ':scientist' => $scientist));
       
    $res = $stmt->fetch();

    ChromePhp::log($res);

    echo json_encode($res);

?>


