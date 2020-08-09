<?php

namespace App\Http\Controllers;

use App\Exports\ResultPairsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

use App\ResultPair;
use App\ExperimentResult;

use DB;

class ResultPairsController extends Controller
{
  /**
   *
   */
  public function index ($id)
  {
    $paired_results = \App\ExperimentResult
      // ::with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected')
      ::find($id)
      ->paired_results;

    $data = [];
    foreach ($paired_results as $result) {
      $arr = [];
      array_push($arr, $result->picture_selected);
      array_push($arr, $result->picture_left);
      array_push($arr, $result->picture_right);

      array_push($data, $arr);
    }

    return response($data, 200);
  }

  /**
   * Export a listing of the resource in a CSV file.
   *
   * @return \Maatwebsite\Excel\Facades\Excel
   */
  public function export (Request $request) {
    // TODO: check if scientist owns experiment and that results belong to experiment
    $results = [];
    $expID = 8;

    if ($request->flags['results']) {
      # get all paired results for each observer
      $observers =
        ExperimentResult
          ::with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected', 'user')
          // ::with('user')
          // ->where('experiment_id', $request->experiment)
          ->whereIn('id', $request->selected)
          ->get();

      # create array in a export ready format
      $data = [];
      foreach ($observers as $observer) {
        foreach ($observer->paired_results as $key => $result) {
          $arr = [];
          $arr['observer']    = $observer->user_id;
          $arr['session']     = $observer->id;
          $arr['left']        = $result->picture_left->name;
          $arr['right']       = $result->picture_right->name;
          $arr['selected']    = $result->picture_selected->name;
          $arr['time_spent']  = $result->client_side_timer;

          array_push($data, $arr);
        }
      }

      $results['results'] = $data;
    }

    if ($request->flags['observerInputs']) {
      $experiment_observer_meta_results = DB::table('experiment_observer_meta_results')
        ->join('observer_metas', 'experiment_observer_meta_results.observer_meta_id', '=', 'observer_metas.id')
        ->where('experiment_observer_meta_results.experiment_id', $expID)
        ->get([
          'experiment_observer_meta_results.user_id',
          'observer_metas.meta',
          'experiment_observer_meta_results.answer'
        ]);

      $data = [];
      foreach ($experiment_observer_meta_results as $result)
      {
        $arr = [];
        $arr['observer'] = $result->user_id;
        $arr['meta']     = $result->meta;
        $arr['answer']   = $result->answer;
        array_push($data, $arr);
      }

      $results['observerInputs'] = $data;
    }

    if ($request->flags['observerMeta']) {
      // array_push($data, $);
      // 8
      // $observers =
      //   ExperimentResult
      //     ::with('user')
      //     // ->where('experiment_id', $request->experiment)
      //     ->whereIn('id', $request->selected)
      //     ->get();
    }

    if ($request->flags['imageSets']) {
      # get the image sets used in a experiment
      $sets = DB::table('experiment_queues')
        ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
        ->leftJoin('picture_sets', 'experiment_sequences.picture_set_id', '=', 'picture_sets.id')
        // ->leftJoin('pictures', 'picture_sets.id', '=', 'pictures.picture_set_id')
        // ->with('pictures')
        ->where([
          ['experiment_queues.experiment_id', '=', $expID],
          ['experiment_sequences.picture_queue_id', '!=', null]
        ])
        ->get();

      $data = [];
      foreach ($sets as $set) {
        $arr = [];
        $arr['set'] = $set;
        $arr['images'] = \App\Picture::where('picture_set_id', $set->id)->get();

        array_push($data, $arr);
      }

      $results['imageSets'] = $data;

      // $experiment_sequences = \App\ExperimentQueue::with('experiment_sequences')->where('experiment_id', '=', 8)->get();

      // $result = DB::table('experiment_sequences')
      //   ->join('picture_sequences', 'picture_sequences.picture_queue_id', '=', 'experiment_sequences.picture_queue_id')
      //   ->join('pictures', 'picture_sequences.picture_id', '=', 'pictures.id')
      //   ->where('experiment_sequences.id', $sequence->id)
      //   ->get(['picture_sequences.*', 'pictures.path', 'pictures.name', 'pictures.is_original', 'pictures.picture_set_id']);

      // return response($data, 200);
    }

    // return response($results, 200);

    $file_ext = 'xlsx'; //in_array($needle, []) // returns true
    // TODO: pre/append user_id or created_at to filename
    $filename = 'results.' . $file_ext;

    # see: https://docs.laravel-excel.com/3.1/exports/
    return Excel::download(new ResultPairsExport($results), $filename);
  }


  public function all () {
    // get alle som tilhører en experiment results som har experiment_id = value
    // definerer reletationship i model... mange til mange forhold?
    return ResultPair::where('experiment_result_id', $id)->get();
  }

  public function statistics (int $id) {
    $results = [];

    # get the image sets used in a experiment
    $sets = DB::table('experiment_queues')
      ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
      ->leftJoin('picture_sets', 'experiment_sequences.picture_set_id', '=', 'picture_sets.id')
      ->where([
        ['experiment_queues.experiment_id', '=', $id],
        ['experiment_sequences.picture_queue_id', '!=', null]
      ])
      ->get();
    $results['imageSets'] = $sets;


    # each image set with belonging images
    $d = [];
    foreach ($sets as $set) {
      $dd = \App\Picture::where([
        ['picture_set_id', '=', $set->picture_set_id],
        ['is_original', '=', 0]
      ])->get();

      array_push($d, $dd);
    }
    $results['imagesForEachImageSet'] = $d;


    # original images
    foreach ($sets as $key => $set) {
      $dd = \App\Picture::where([
        ['picture_set_id', '=', $set->picture_set_id],
        ['is_original', '=', 1]
      ])->first();

      $results['imageUrl'][$key] = $dd;
    }


    $paired_results = ExperimentResult
      ::with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected')
      ->where('experiment_id', $id)
      ->get();

    $data = [];
    foreach ($paired_results as $result)
    {
      foreach ($result->paired_results as $res)
      {
        $arr = [];
        $arr['left']  = $res->picture_left->id;
        $arr['right'] = $res->picture_right->id;

        # selected image
        $arr['pictureId'] = $res->picture_selected->id;
        $arr['name']      = $res->picture_selected->name;

        $arr['won'] = 1; // if none chosen = 0?     @param  {int} $points   amount of points to add... 1 for paired?
        // $arr['po'] = 3;

        # if right was selected, it won against left
        if ($arr['pictureId'] == $arr['right']) {
          $arr['wonAgainst'] = $res->picture_left->id;
          $arr['wonAgainstName'] = $res->picture_left->name;
        } else {
          $arr['wonAgainst'] = $res->picture_right->id;
          $arr['wonAgainstName'] = $res->picture_right->name;
        }

        array_push($data, $arr);
      }
    }

    # group selected image results by image set
    $new = [];
    foreach ($data as $result) {
      foreach ($results['imagesForEachImageSet'] as $key => $images) {
        foreach ($images as $image) {
          if ($result['pictureId'] == $image['id']) {
            $new[$key][] = $result;
          }
        }
      }
    }
    $results['resultsForEachImageSet'] = $new;


    return response($results);
  }


  public function store (Request $request) {
    $result = ResultPair::create([
      'experiment_result_id'  => $request->experiment_result_id,
      'picture_id_selected'   => $request->picture_id_selected,
      'picture_id_left'       => $request->picture_id_left,
      'picture_id_right'      => $request->picture_id_right,
      'client_side_timer'     => $request->client_side_timer,
      'chose_none'            => $request->chose_none
    ]);

    if ($result) {
      return response('result_stored', 201);
    }
  }
}
