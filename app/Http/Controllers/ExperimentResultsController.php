<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use DB;

use App\ExperimentResult;

class ExperimentResultsController extends Controller
{
    public function store (Request $request) {
      $experimentResult = ExperimentResult::create([
        'user_id' => auth()->user()->id,
        'experiment_id' => $request->experimentId,
        'start_time' => time() // or microtime()
      ]);

      if ($experimentResult) {
        return response($experimentResult, 201);
      }
    }

    public function destroy (Request $request, Experiment $experiment) {
      // $results = $request->results;

      $userResults = auth()->user()->results->map(function ($result) {
        return $result->id;
      });

      $valid = collect($results)->every(function ($value, $key) use ($userResultIds) {
        return $userResultIds->contains($value);
      });

      if (!$valid) {
        return response()->json('Unauthorized', 401);
      }

      Result::destroy($request->results);

      return response()->json('Deleted', 200);
    }

    public function fetch ($id) {
      return \App\ExperimentResult::where('experiment_id', $id)
        ->get();
    }

    /**
     *
     */
    public function index ($experiment_id) {
      $results = \App\Experiment::find($experiment_id)->results()->get();
      return response($results);

      // $comment = \App\Result::find(18);
      // return $comment->experiment->title;

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

      # gets all imageset id's
      $sql = "SELECT picture_sets.id, picture_sets.title FROM experiments "
      . " JOIN experiment_queues    ON experiment_queues.experiment_id       = experiments.id "
      . " JOIN experiment_sequences ON experiment_queues.id                  = experiment_sequences.experiment_queue_id "
      . " JOIN picture_queues       ON experiment_sequences.picture_queue_id = picture_queues.id "
      . " JOIN picture_sequences    ON picture_queues.id                     = picture_sequences.picture_queue_id "
      . " JOIN pictures             ON picture_sequences.picture_id          = pictures.id "
      . " JOIN picture_sets         ON pictures.picture_set_id               = picture_sets.id "
      . " WHERE experiments.id = ? AND experiments.user_id = ? "
      . " GROUP BY id";

      $expId = (int) $experiment_id;
      $user_id = (int) auth()->user()->id;

      $sth = $db->prepare($sql);
      $sth->bindParam(1, $expId);
      $sth->bindParam(2, $user_id);
      $sth->execute();
      $imageSets = null;
      $imageSets = $sth->fetchAll();

      $sql = "SELECT experiment_sequences.id FROM experiments "
      . " JOIN experiment_queues    ON experiment_queues.experiment_id     = experiments.id "
      . " JOIN experiment_sequences ON experiment_queues.id                = experiment_sequences.experiment_queue_id "
      . " JOIN picture_queues       ON experiment_sequences.picture_queues = picture_queues.id "
      . " JOIN picture_sequences    ON picture_queues.id                   = picture_sequences.picture_queue_id "
      . " JOIN pictures             ON picture_sequences.picture_id        = pictures.id "
      . " JOIN picture_sets         ON picture.picture_set_id              = picture_sets.id "
      . " WHERE experiments.id = ? AND experiments.user_id = ?  "
      . " GROUP BY picture_sequences.picture_queue_id";


      foreach ($experiment_sequences as $experiment_sequence) {
        $sql = "SELECT pictures.name, pictures.id, pictures.picture_set_id FROM pictures " .
        "JOIN picture_sequences    ON pictures.id                              = picture_sequences.picture_id " .
        "JOIN picture_queues       ON picture_sequences.picture_queue_id       = picture_queues.id " .
        "JOIN experiment_sequences ON experiment_sequences.picture_queues      = picture_queues.id " .
        "JOIN experiment_queues    ON experiment_sequences.experiment_queue_id = experiment_queues.id " .
        "JOIN experiment           ON experiment_queues.experiment_id          = experiments.id " .
        "WHERE experiments.id = ? AND experiments.user_id = ? AND experiment_sequences.id = ?" .
        "GROUP BY pictures.id";

        $sth = $db->prepare($sql);
        $sth->bindParam(1, $experimentId);
        $sth->bindParam(2, $_SESSION['user']['id']);
        $sth->bindParam(3, $experimentOrder['eOrder']);
        $sth->execute();
        $imageSetImages[] = $sth->fetchAll();
      }

      // $imageSets = DB::table('experiments')
      //   ->join('experiment_queues',    'experiment_queues.experiment_id',       '=', 'experiments.id')
      //   ->join('experiment_sequences', 'experiment_queues.id',                  '=', 'experiment_sequences.experiment_queue_id')
      //   ->join('picture_queues',       'experiment_sequences.picture_queue_id', '=', 'picture_queues.id')
      //   ->join('picture_sequences',    'picture_queues.id',                     '=', 'picture_sequences.picture_queue_id')
      //   ->join('pictures',             'picture_sequences.picture_id',          '=', 'pictures.id')
      //   ->join('picture_sets',         'pictures.picture_set_id',               '=', 'picture_sets.id')
      //   ->where([
      //     ['experiments.id', $expId],
      //     ['experiments.user_id', $user_id]
      //   ])
      //   ->get();

        return $imageSets;
    }

    public function paired_results () {
      $db = new PDO(
        'mysql:host=127.0.0.1;' .
        'dbname=passport;',
        'root',
        ''
      );
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");

      $result = [];
      $pairResults = [];

      foreach ($experimentOrders as $experimentOrder)
      {
        $sql = "SELECT pictures.name,
        pictures.id AS picture_id,
        picture_sequences.id AS p_seq_id,
        picture_sequences.picture_order po,
        COUNT(results.picture_order_id) AS won,
        results.chose_none,
        experiment_sequences.id AS e_seq_id,
        result.personId,
        result.created, "
        . "(SELECT pictures.id "
            . " FROM pictures "
            . " JOIN picture_sequences ON pictures.id = picture_sequences.picture_id "
            . " JOIN picture_queues ON picture_sequences.picture_queue_id = picture_queues.id "
            . " JOIN experiment_sequences ON picture_queues.id = experiment_sequences.picture_queue_id "
            . " WHERE picture_sequences.picture_order = po AND experiment_sequences.id = e_seq_id AND pictures.id != picture_id "
            . " LIMIT 0,1) AS wonAgainst, "
      . "(SELECT pictures.name "
          . " FROM pictures "
          . " JOIN picture_sequences ON pictures.id = picture_sequences.picture_id "
          . " JOIN picture_queues ON picture_sequences.picture_queue_id = picture_queues.id "
          . " JOIN experiment_sequences ON picture_queues.id = experiment_sequences.picture_queue_id "
          . " WHERE picture_sequences.picture_order = po AND experiment_sequences.id = e_seq_id AND pictures.id != picture_id "
          . " LIMIT 0,1) AS wonAgainstName "
        . "FROM picture_sequences "
        . "JOIN picture_queues ON picture_sequences.picture_queue_id = picture_queues.id "
        . "JOIN experiment_sequences ON picture_queues.id = experiment_sequences.picture_queue_id "
        . "JOIN experiment_queues on experiment_sequences.experiment_queue_id = experiment_queues.id "
        . "JOIN experiments ON experiments.id = experiment_queues.experiment_id "
        . "JOIN pictures ON picture_sequences.picture_id = pictures.id "
        . "LEFT JOIN results ON picture_sequences.id = results.picture_order_id "
        . (($complete == 1) ? " JOIN experiment_results ON results.experiment_id = experiment_results.experiment_id
            AND experiment_results.user_id = results.user_id " : ' ')
        . "WHERE experiments.id = ?
            AND experiments.user_id = ?
            AND experiment_sequences.id = ?
            AND created_at IS NOT NULL ". (($complete == 1) ? " AND experiment_results.complete != 1 " : " ")
        . "GROUP BY picture_order_id, user_id "
        . "ORDER BY picture_order_id ";

        $sth = $db->prepare($sql);
        $sth->bindParam(1, $experimentId);
        $sth->bindParam(2, $_SESSION['user']['id']);
        $sth->bindParam(3, $experimentOrder['eOrder']);
        $sth->execute();
        $pairResults[] = $sth->fetchAll();
      }

      $result[] = $pairResults;
    }
}
