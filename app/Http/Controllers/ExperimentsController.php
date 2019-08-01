<?php

namespace App\Http\Controllers;

use App\Experiment;
use Illuminate\Http\Request;
use PDO;
use DB;

use App\ObserverMeta;
use App\ExperimentObserverMeta;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)->get();
    }

    public function all () {
      return Experiment::orderBy('id', 'desc')->get();
    }

    public function find (Request $request) {
      return Experiment::find($request->id);
    }
    
    public function findIsOwner (Request $request) {
      return Experiment::where('user_id', auth()->user()->id)
        ->where('id', $request->id)
        ->get();
    }

    public function store (Request $request) {
      $data = $request->validate([
        'title'          => 'required|string',
        'experimentType' => 'required'
      ]);

      $experiment = Experiment::create([
        'user_id'           => auth()->user()->id,
        'title'             => $request->title,
        'experiment_type_id'=> $request->experimentType,
        'short_description' => $request->shortDescription,
        'long_description'  => $request->longDescription,
        'is_public'         => $request->isPublic,
        'same_pair'         => $request->samePairTwice
      ]);

      if ($experiment->id > 0)
      {
        $experiment_queue = \App\ExperimentQueue::create([
          'experiment_id' => $experiment->id
        ]);

        if ($experiment_queue->id > 0)
        {
          foreach ($request->observerMetas as $meta)
          {
            if ($meta['type'] == 'meta') {
              $observerMeta = ObserverMeta::create([
                'user_id' => auth()->user()->id,
                'meta'    => $meta['value']
              ]);

              ExperimentObserverMeta::create([
                'experiment_id'    => $experiment->id,
                'observer_meta_id' => $observerMeta->id
              ]);
            }

            if ($meta['type'] == 'metaFromHistory') {
              ExperimentObserverMeta::create([
                'experiment_id'    => $experiment->id,
                'observer_meta_id' => $meta['value']
              ]);
            }
          }

          foreach ($request->sequences as $step)
          {
            if ($step['type'] === 'imageSet')
            {
              if ($request->algorithm === 'Random Queue') // save algo in experiment table and use $experiment->algorithm instead?
              {
                if ($experiment->experiment_type_id == 1)
                {
                  $picture_queue_id = $this->random_queue(
                    $step['type'],
                    $experiment->same_pair,
                    $step['value']
                  );
                }
                else # Category Rating
                {
                  $picture_queue_id = $this->make_category_queue( $step['value'] );
                }

                $this->add_queue_to_experiment(
                  $experiment_queue->id,
                  $picture_queue_id,
                  null
                );
              }
            }
            else if ($step['type'] === 'instruction')
            {
              $instruction = \App\Instruction::create([
                'user_id' => auth()->user()->id,
                'description' => $step['value']
              ]);

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $instruction->id
              );
            }
            else if ($step['type'] === 'instructionFromHistory')
            {
              // TODO: check that the user owns the instruction

              $this->add_queue_to_experiment(
                $experiment_queue->id,
                null,
                $step['value']
              );
            }
          }

          return response($experiment, 201);
        } else {
          return response('Experiment queue could not be created.', 404);
          // IF SOMETHING FAILS WE SHOULD DELETE THE EXPERIMENT
          // $experiment->delete();
        }
      } else {
        return response('Experiment could not be created.', 404);
      }
    }

    /**
     * 
     */
    protected function random_queue ($type, $twice, $imageSetId) {
      $images = \App\Picture::where([
        ['picture_set_id', $imageSetId],
        ['is_original', 0]
      ])->get();

      // $algorithm = new \App\Classes\Algorithms();
      // $algorithm->make_queue($images, $twice);
      $pairs = $this->make_queue($images, $twice);

      $picture_queue = \App\PictureQueue::create([
        'title' => NULL
      ]);

      # construct an array of all picture sequences
      $order = 0;
      $queries = [];
      foreach ($pairs as $pair) {
        array_push(
          $queries,
          array('picture_order' => $order, 'picture_id' => $pair[0], 'picture_queue_id' => $picture_queue->id),
          array('picture_order' => $order, 'picture_id' => $pair[1], 'picture_queue_id' => $picture_queue->id)
        );

        $order++;
      }

      \App\PictureSequence::insert($queries);

      return $picture_queue->id;
    }

    protected function make_category_queue ($image_set_id) {
      $picture_queue = \App\PictureQueue::create([
        'title' => NULL
      ]);

      $images = \App\Picture::where([
        ['picture_set_id', $image_set_id],
        ['is_original', 0]
      ])->get();

      $queries = [];
      foreach ($images as $image) {
        array_push(
          $queries,
          array('picture_order' => 0, 'picture_id' => $image['id'], 'picture_queue_id' => $picture_queue->id)
        );
      }

      \App\PictureSequence::insert($queries);

      return $picture_queue->id;
    }

    /**
     *
     */
    protected function add_queue_to_experiment ($experiment_queue_id, $picture_queue_id = null, $instruction_id = null)
    {
      // $experiment_queue = \App\ExperimentQueue::where('experiment_id', $experiment_id)->first();

      // $experiment_queue = \App\ExperimentQueue::create([
      //   'experiment_id' => $experiment_id
      // ]);

      $experiment_sequence = \App\ExperimentSequence::create([
        'experiment_queue_id' => $experiment_queue_id,
        'picture_queue_id'    => $picture_queue_id,
        'instruction_id'      => $instruction_id
      ]);

      return $experiment_sequence;
    }

    public function update (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'title' => 'required|string',
        'experiment_type' => 'required'
      ]);

      $experiment->update($data);

      return response($experiment, 200);
    }

    /**
     * Set whether the experiment should be visible to the public or not.
     */
    public function visibility (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
        'is_public' => 'required|boolean'
      ]);

      $experiment->update($data);

      return response($experiment, 200);

      // Experiment::where('user_id', auth()->user()->id)->update($data);
    }

    public function destroy (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $experiment->delete();

      return response('Deleted experiment', 200);
    }

    public function start ($id) {
      session_start();
      $db = new PDO('mysql:host=127.0.0.1;' . 'dbname=passport;', 'root', '');
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
      $db->query("SET NAMES 'utf8'");

      // $result = DB::table('experiment_queues')
      //   ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
      //   ->where('experiment_queues.experiment_id', $id)
      //   ->get();

      $sql = "SELECT * FROM experiment_queues
        JOIN experiment_sequences ON experiment_sequences.experiment_queue_id = experiment_queues.id
        WHERE experiment_queues.experiment_id = ?
        ORDER BY experiment_sequences.id ASC;";

      $sth = $db->prepare($sql);
      $sth->bindParam(1, $id);
      $sth->execute();
      $result = $sth->fetchAll();

      $all = [];
      foreach ($result as $sequence)
      {
        if ($sequence['picture_queue_id'] != null) {
          $sql = "
            SELECT * FROM experiment_sequences
            JOIN picture_sequences ON picture_sequences.picture_queue_id = experiment_sequences.picture_queue_id
            WHERE experiment_sequences.id = ?;
          ";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $sequence['id']);
          $sth->execute();
          $result = $sth->fetchAll();

          # Shuffle the picture queue every time we fetch it,
          # to make sure every observer gets a different queue.
          $Algorithms = new \App\Classes\Algorithms;
          $result = $Algorithms->shuffle_the_cards($result);

          array_push($all, $result);
        }
        
        if ($sequence['instruction_id'] != null) {
          $sql = "
            SELECT * FROM experiment_sequences
            JOIN instructions ON instructions.id = experiment_sequences.instruction_id
            WHERE experiment_sequences.id = ?;
          ";

          $sth = $db->prepare($sql);
          $sth->bindParam(1, $sequence['id']);
          $sth->execute();
          $result = $sth->fetchAll();

          array_push($all, $result);
        }
      }

      return response($all);
    }

    public function next_step () {
      session_start();

      if ($_SESSION['activeObserverExperiment'] == null) {
        echo json_encode(array("type" => "finished"));
        exit;
      }

      // $exType = getExperimentType($db);
      $exType = 'pair';
      // pictureQueue er aktiv, sjekk om det finnes flere bilder i pictureQueuen.
      if ( $_SESSION['activeObserverExperiment']['pictureSequence'] != null )
      {
        $index = $_SESSION['activeObserverExperiment']['activePictureSequence'][count($_SESSION['activeObserverExperiment']['activePictureSequence'])-1];

        if ($index < count($_SESSION['activeObserverExperiment']['pictureSequence']) - 1)
        {
          if ($exType == "pair") {
            $_SESSION['activeObserverExperiment']['activePictureSequence'][0] += 2;
            $_SESSION['activeObserverExperiment']['activePictureSequence'][1] += 2;
            writeOutPicture();
          } else if ($exType == "category") {
            $_SESSION['activeObserverExperiment']['activePictureSequence'][0] += 1;
            writeOutPictureForCategory();
          } else if ($exType == "artifact") {
            $_SESSION['activeObserverExperiment']['activePictureSequence'][0] += 1;
            writeOutPictureForCategory();
          } else if ($exType == "triplet") {
            $_SESSION['activeObserverExperiment']['activePictureSequence'][0] += 3;
            $_SESSION['activeObserverExperiment']['activePictureSequence'][1] += 3;
            $_SESSION['activeObserverExperiment']['activePictureSequence'][2] += 3;
            writeOutPictureTriplet();
          }
        } else { //ingen flere bilder igjen
          $_SESSION['activeObserverExperiment']['activePictureSequence'] = null;
          $_SESSION['activeObserverExperiment']['pictureSequence'] = null;
          finished_current_experiment_sequence();
        }
      }
      else {
        finished_current_experiment_sequence();
      }
    }

    /**
     * This function is run every time an experimentSequence is finished.
     * Will return pictures, or instruction depending on what current experimentSequence is.
     */
    protected function finished_current_experiment_sequence () {
      // $exType = getExperimentType($db);
      $exType = 'pair';

      /**
      experiment starts

        load experiment_sequences

        finished experiment sequence
            if next sequence is image set -> return picture queue
                get all pictures for picture queue
                shuffle
            if instructions -> return instructions


      Notes:
        - Need to shuffle every queue seperatly
        - Either use localstorage or session so that process is not lost on page refresh
        - postResults("pair", pictureOrderId, choose) needs to happen on every action
      */

      if ($_SESSION['activeObserverExperiment']['index'] <= (count($_SESSION['activeObserverExperiment']['experimentSequence'])-1))
      {
        $index = $_SESSION['activeObserverExperiment']['index'];
        $_SESSION['activeObserverExperiment']['activeExperimentSequence'] = $_SESSION['activeObserverExperiment']['experimentorder'][$index];
        $_SESSION['activeObserverExperiment']['index']++;

        // the next step in the experiment is a picture queue (experimentSequence has a picture_queue_id value)
        if ($_SESSION['activeObserverExperiment']['activeExperimentSequence']['pictureQueue'] != null)
        {
          new_picture_queue();

          if ($exType == "pair") {
            // writeOutPicture(); ->
            $picture1 = $_SESSION['activeObserverExperiment']['activePictureSequence'][0];
            $picture2 = $_SESSION['activeObserverExperiment']['activePictureSequence'][1];
            $pictures[0]['pictureId'] =  $_SESSION['activeObserverExperiment']['pictureSequence'][$picture1]['picture']; //Gets ID from database for picture
            $pictures[0]['pictureOrderId'] = $_SESSION['activeObserverExperiment']['pictureSequence'][$picture1]['id'];

            $pictures[1]['pictureId'] =  $_SESSION['activeObserverExperiment']['pictureSequence'][$picture2]['picture']; //Gets ID from database for picture
            $pictures[1]['pictureSequenceId'] = $_SESSION['activeObserverExperiment']['pictureSequence'][$picture2]['id'];

            echo json_encode(array("type" => "pictureQueue", "pictureQueue" => $pictures));
            exit;
          } else if ($exType == "category") {
            // writeOutPictureForCategory(); ->
            $pictures = array();
            $picture1 = $_SESSION['activeObserverExperiment']['activePictureOrder'][0]; //Current index
            $pictures[0]['pictureId']       = $_SESSION['activeObserverExperiment']['pictureOrder'][$picture1]['picture'];
            $pictures[0]['pictureOrderId']  = $_SESSION['activeObserverExperiment']['pictureOrder'][$picture1]['id'];

            echo json_encode(array("type" => "pictureQueue","pictureQueue" => $pictures));
            exit;
          } else if ($exType == "rating") {
            writeOutPictureForRating();
          } else if ($exType == "artifact") {
            writeOutPictureForCategory();
          } else if ($exType == "triplet") {
            writeOutPictureTriplet();
          }
        }
        // the next step in the experiment is instructions (experimentSequence has a instruction_id value) 
        else if ($_SESSION['activeObserverExperiment']['activeExperimentSequence']['instruction'] != null) {
          // writeExperimentInstruction(); ->
          $sql = "
            SELECT * FROM experiment_sequence
            JOIN instruction ON experiment_sequence.instruction_id = instruction.id
            WHERE experiment_sequences.id = ?;
          ";
          $sth = $db->prepare($sql);
          $sth->bindParam(1, $_SESSION['activeObserverExperiment']['activeExperimentSequence']['id']);
          $sth->execute();
          $result = $sth->fetch();
          echo json_encode([
            "type" => "experimentinstruction",
            "experimentinstruction" => $result['text']
          ]);
        }
      }
      // whole experiment is finished, no more experiment sequences
      else {
        $_SESSION['activeObserverExperiment'] = null;
        echo json_encode(array("type" => "finished"));
        exit;
      }
    }

    /**
     * This function is run every time you start a new pictureQueue for an experiment
     * Will set up correct order for pictures, and store it in session.
     */
    public function new_picture_queue () {
      session_start();

      $db = new PDO(
        'mysql:host=127.0.0.1;' .
        'dbname=passport;',
        'root',
        ''
      );

      // $exType = getExperimentType();
      $exType = 'pair';

      //Må muligens adde ORDER BY pOrder her ? // ORDER BY picture_sequence.id
      //This SQL gets ALL the pictures for a given pictureQueue.
      $sql = "
        SELECT * FROM experiment_sequences
        JOIN picture_sequences ON picture_sequences.picture_queue_id = experiment_sequences.picture_queue_id
        -- JOIN instruction ON experiment_sequence.instruction_id = instruction.id
        WHERE experiment_sequences.id = ?;
      ";

      // $result = DB::table('experiment_sequences')
      //   ->join('picture_sequences', 'picture_sequences.picture_queue_id ', '=', 'experiment_sequences.picture_queue_id')
      //   ->where('experiment_sequences.id', $_SESSION['activeObserverExperiment']['activeExperimentOrder']['id'])
      //   ->get();
      $sth = $db->prepare($sql);
      $sth->bindParam(1, $_SESSION['activeObserverExperiment']['activeExperimentSequence']['id']);
      $sth->execute();
      $result = $sth->fetchAll();

      if ($exType == "pair") {
        // $PairComparison = \App\Classes\PairComparison;
        // $result = $PairComparison->shuffle_the_cards();
        $result = shuffleTheCards($result);
      } else if ($exType == "triplet") {
        $result = shuffleTheCardsTriplet($result);
      } else {
        shuffle($result);
      }

      $_SESSION['activeObserverExperiment']['pictureSequence'] = $result;

      //Pair comparison will always return two pictures.  "0" and "1" are indexes in the stored array.
      if ($exType == "pair") {
        $_SESSION['activeObserverExperiment']['activePictureSequence'] = array(0 => 0, 1 => 1);
      }
      //Category will always return ALL pictures, or just one.
      else if ($exType == "category") {
        shuffle($result);
        $_SESSION['activeObserverExperiment']['activePictureSequence'] = array(0 => 0);
      }
      // artifact will always return ALL pictures, or just one.
      else if ($exType == "artifact") {
        shuffle($result);
        $_SESSION['activeObserverExperiment']['activePictureSequence'] = array(0 => 0);
      }
      //
      else if ($exType == "triplet") {
        $_SESSION['activeObserverExperiment']['activePictureSequence'] = array(0 => 0, 1 => 1, 2 => 2);
      }
    }

    /**
     * Algorithm for generating a image experiment queue.
     */
    private function make_queue ($images, $imagesShownRightAndLeft) {
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
