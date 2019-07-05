<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PictureQueuesController extends Controller
{
    public function create () {
      $db = new PDO(
        'mysql:host=127.0.0.1;' .
        'dbname=passport;',
        'root',
        ''
      );
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
      // ensures what client is sending to server and respons from server is encoded in UTF-8
      $db->query("SET NAMES 'utf8'");

      $option = $_GET['option']; // generateRandom, ratingCategory

      if ($option == "generateRandom") {
        $rightAndLeft = $_GET['rightAndLeft']; // In case the scientist chooses to view pictures both right and left
        $imagesetId = $_GET['imagesetId'];

        // get all images for a picture set
        $sql = "SELECT * FROM picture WHERE picture_set_id = ? AND is_original = 0";
        $sth = $db->prepare($sql);
        $sth->bindParam(1, $imagesetId);
        $sth->execute();
        $images = $sth->fetchAll();

        // generate array with new queue
        $pairs = $this->make_queue($images, $rightAndLeft);

        // insert the queue into the db
        $db->beginTransaction();
        $sql = "INSERT INTO `picture_queue` (`id`, `title`) VALUES (NULL, NULL);";
        $sth = $db->prepare($sql);
        $sth->execute();
        $pictureQueueId = $db->lastInsertId();
        $order = 0;
        foreach ($pairs as $pair) {
          $sql = "INSERT INTO `picture_sequence` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (?, ?, ?);";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $order);
          $sth->bindParam(2, $pair[0]);
          $sth->bindParam(3, $pictureQueueId);
          $sth->execute();
          $sql = "INSERT INTO `picture_sequence` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (?, ?, ?);";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $order);
          $sth->bindParam(2, $pair[1]);
          $sth->bindParam(3, $pictureQueueId);
          $sth->execute();
          $order++;
        }
        $db->commit();

        echo json_encode($pictureQueueId);
        exit;
      }
      else if ($option == "ratingCategory") { // save with eOrder 0
        $db->beginTransaction();
        try {
          $imagesetId = $_GET['imagesetId'];

          $sql = "INSERT INTO `picture_queue` (`id`, `title`) VALUES (NULL, NULL);";
          $sth = $db->prepare($sql);
          $sth->execute();
          $pictureQueueId = $db->lastInsertId();

          $sql = "SELECT * FROM picture WHERE picture_set = ? AND is_original = 0;";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $imagesetId);
          $sth->execute();
          $result = $sth->fetchAll();
          foreach ($result as $image) {
            $sql = "INSERT INTO `picture_sequence` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (0, ?, ?);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $image['id']);
            $sth->bindParam(2, $pictureQueueId);
            $sth->execute();
          }
          $db->commit();

          echo json_encode($pictureQueueId);
          exit;
        } catch (Exception $e) {
          $db->rollBack();

          echo json_encode(0);
          exit;
        }
      }
    }

    /**
     * A function that will create a pictureQueue.
     * @param $images an array with imageId's of which to create a queue from.
     * @param $images ShownRightAndLeft 1/0 of whether to show a picture on both sides.
     *
     * @return $pairs, an array with the new queue.
     */
    protected function make_queue($images, $imagesShownRightAndLeft) {
      $pairs = [];
      $index = 1;
      $arrIndex = 0;

      foreach ($images as $image) {
        for ($i = $index; $i < count($images); $i++ ) {
          $pairs[$arrIndex][0] = $image['id'];
          $pairs[$arrIndex][1] = $images[$i]['id'];
          if ($imagesShownRightAndLeft == 1) {
            $arrIndex++;
            $pairs[$arrIndex][0] = $images[$i]['id'];
            $pairs[$arrIndex][1] = $image['id'];
          }
          $arrIndex++;
        }
        $index++;
      }
      shuffle($pairs);

      return $pairs;
    }
}
