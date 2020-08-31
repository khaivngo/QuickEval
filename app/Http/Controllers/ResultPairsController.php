<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResultPairsExport;

use Illuminate\Http\Request;

use App\ResultPair;
use App\ExperimentResult;
use App\ExperimentQueue;
use App\Experiment;
use App\ResultObserverMeta;
use App\ExperimentObserverMeta;

use DB;

class ResultPairsController extends Controller
{
  /**
   *
   */
  public function index ($id)
  {
    $paired_results = ExperimentResult
      ::find($id)
      // ->with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected')
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
    $expID = $request->experimentId;

    # get all paired results for each selected observer
    if ($request->flags['results']) {
      $observers =
        ExperimentResult
          ::with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected', 'user')
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

    # get all the image sets, with images, used in the experiments experiment_sequences
    if ($request->flags['imageSets']) {
      $data =
        ExperimentQueue::with(['experiment_sequences' => function ($query) {
            $query->where('experiment_sequences.picture_queue_id', '!=', NULL)
              ->with('picture_set.pictures');
        }])
        ->where('experiment_id', '=', $expID)
        ->get();

      // return $data[0]->experiment_sequences;

      $results['imageSets'] = $data[0]->experiment_sequences;
    }

    # get observer input answers for selected observers
    if ($request->flags['inputs']) {
      $result_observer_metas =
        ResultObserverMeta::with('observer_meta', 'experiment_result.user')
          ->whereIn('experiment_result_id', $request->selected)
          ->get();

      $results['inputs'] = $result_observer_metas;
    }

    # get all meta data for observer input fields used in the experiment
    if ($request->flags['inputsMeta']) {
      $results['inputsMeta'] = ExperimentObserverMeta::with('observer_meta')->where('experiment_id', $expID)->get();
    }

    # get meta data about experiment
    if ($request->flags['expMeta']) {
      $expMeta = Experiment::find($expID);

      # create array in a export ready format
      $data = [];
      $data['title']            = ['title', $expMeta->title];
      $data['experiment_type']  = ['experiment type', $expMeta->experiment_type->name];
      $data['delay']            = ['delay between stimuli switching', $expMeta->delay];
      $data['background_colour']= ['Background colour', $expMeta->background_colour];
      $data['stimuli_spacing']  = ['Stimuli spacing', $expMeta->stimuli_spacing . 'px'];
      $data['same_pair']        = ['Same pair twice (flipped)', ($expMeta->same_pair == 1) ? 'yes' : 'no'];
      $data['show_original']    = ['Show original', ($expMeta->show_original == 1) ? 'yes' : 'no'];

      $results['expMeta'] = $data;
    }

    # use the user selected file format if it exists in whitelist array, else default to CSV
    $file_ext = in_array($request->fileFormat, ['csv','xlsx', 'html']) ? $request->fileFormat : 'csv';
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

    # REPLACE WITH RELATIONSHIP QUERY ABOVE!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
        ['is_original', '=', 0] // filter out any original
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
