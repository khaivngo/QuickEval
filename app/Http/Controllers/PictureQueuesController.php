<?php

namespace App\Http\Controllers;
use PDO;
use App\PictureQueue;

use Illuminate\Http\Request;

class PictureQueuesController extends Controller
{
    public function store (Request $request) {
      // $type = $request->type;
      // $rightAndLeft = $request->rightAndLeft;
      // $imageSetId = $request->imageSetId;

      $rightAndLeft = 1; // In case the scientist chooses to view pictures both right and left
      $imagesetId = 12;
      $option = "ratingCategory";
      // $option = $_GET['option']; // generateRandom, ratingCategory

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

      if ($option == "generateRandom") {
        // get all images for a picture set
        $sql = "SELECT * FROM pictures WHERE picture_set_id = ? AND is_original = 0;";
        $sth = $db->prepare($sql);
        $sth->bindParam(1, $imagesetId);
        $sth->execute();
        $images = $sth->fetchAll();

        // $images = PictureQueue::where('picture_set_id = ? AND is_original = 0', [$imagesetId]);
        // $images = DB::select('SELECT * FROM picture WHERE picture_set_id = ? AND is_original = 0', [$imagesetId]);

        // generate array with new queue
        $pairs = $this->make_queue($images, $rightAndLeft);

        $db->beginTransaction();
        // $sql = "INSERT INTO `picture_queues` (`id`, `title`) VALUES (NULL, NULL);";
        $sql = "INSERT INTO `picture_queues` (`title`) VALUES (NULL);";
        $sth = $db->prepare($sql);
        $sth->execute();
        $pictureQueueId = $db->lastInsertId();
        $order = 0;
        foreach ($pairs as $pair) {
          $sql = "INSERT INTO `picture_sequences` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (?, ?, ?);";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $order);
          $sth->bindParam(2, $pair[0]);
          $sth->bindParam(3, $pictureQueueId);
          $sth->execute();
          $sql = "INSERT INTO `picture_sequences` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (?, ?, ?);";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $order);
          $sth->bindParam(2, $pair[1]);
          $sth->bindParam(3, $pictureQueueId);
          $sth->execute();
          $order++;
        }
        $db->commit();

        return response($pictureQueueId);
      }
      else if ($option == "notRandom") {
        /**
         * Får medsendt en array med to bildeideer.  Disse skal inn i en kanskje eksisterendes pictureQueue OM DEN FINNES
         * Har ikke addet sikkerhetssjekker her.
         * Kan vel egentlig gå ut i fra at denne funksjonen blir kjørt en gang for hvert enkelt bildesett som lages. typ ganske mange ganger
         */
        $rightAndLeft = $_GET['rightAndLeft'];  //In case the scientist chooses to view pictures both right and left.
        $images = $_GET['images'];
        $pictureQueueId = $_GET['pictureQueueId'];  //This is 0 if it is for a new set.
        $imagesArray = array();

        foreach ($images as $image) {
          $sql = "SELECT * FROM picture WHERE id = ?;";
          $sth = $db->prepare($sql);
          $sth->bindParam(1,$image);
          $sth->execute();
          $imagesArray[] = $sth->fetch();
        }

        $pairs = $this->make_queue($imagesArray, $rightAndLeft);
        if (count($pairs > 1)) {
          $db->beginTransaction();
          $order;
          if ($pictureQueueId == 0) {  //Generate new pictureQueue
            // $name = $_GET['name'];
            $name = 'what';
            // $sql = "INSERT INTO `picturequeue` (`id`, `title`) VALUES (NULL, ?);";
            $sql = "INSERT INTO `picture_queues` (`title`) VALUES (NULL);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1,$name);
            $sth->execute();

            $pictureQueueId =  $db->lastInsertId();
            $order = 0;

          } else { //PictureQueue exists.  Need to get order from pictureOrder
            $sql = "SELECT * FROM picture_sequences where picture_queue_id = ? ORDER BY picture_order DESC LIMIT 1";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $pictureQueueId);
            $sth->execute();
            $result = $sth->fetch();
            $order = ($result['pOrder'] + 1);
          }

          foreach($pairs as $pair) {
            // $sql = "INSERT INTO `picture_sequences` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
            $sql = "INSERT INTO `picture_sequences` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (?, ?, ?);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1,$order);
            $sth->bindParam(2,$pair[0]);
            $sth->bindParam(3,$pictureQueueId);
            $sth->execute();

            // $sql = "INSERT INTO `picture_sequences` (`pOrder`, `picture`, `pictureQueue`) VALUES (?, ?, ?);";
            $sql = "INSERT INTO `picture_sequences` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (?, ?, ?);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $order);
            $sth->bindParam(2, $pair[1]);
            $sth->bindParam(3, $pictureQueueId);
            $sth->execute();
            $order++;
          }

          return response($pictureQueueId);
        }
        $db->commit();
      }
      else if ($option == "ratingCategory") { // save with eOrder 0
        try {
          $db->beginTransaction();
          // $sql = "INSERT INTO `picture_queues` (`id`, `title`) VALUES (NULL, NULL);";
          $sql = "INSERT INTO `picture_queues` (`title`) VALUES (NULL);";
          $sth = $db->prepare($sql);
          $sth->execute();
          $pictureQueueId = $db->lastInsertId();

          $sql = "SELECT * FROM pictures WHERE picture_set_id = ? AND is_original = 0;";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $imagesetId);
          $sth->execute();
          $result = $sth->fetchAll();
          foreach ($result as $image) {
            $sql = "INSERT INTO `picture_sequences` (`picture_order`, `picture_id`, `picture_queue_id`) VALUES (0, ?, ?);";
            $sth = $db->prepare($sql);
            $sth->bindParam(1, $image['id']);
            $sth->bindParam(2, $pictureQueueId);
            $sth->execute();
          }
          $db->commit();

          return response($pictureQueueId);
        } catch (Exception $e) {
          $db->rollBack();

          return response("Could NOT generate queue for type: rating");
        }
      }
    }

    /**
     * The algorithm for making a queue.
     *
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
      shuffle($pairs); # https://www.php.net/manual/en/function.shuffle.php

      return $pairs;
    }
}
