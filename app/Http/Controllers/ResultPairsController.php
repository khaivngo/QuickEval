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

    $observers =
      ExperimentResult
        ::with(
          'paired_results.picture_left',
          'paired_results.picture_right',
          'paired_results.picture_selected',
          'user'
        )->whereIn('id', $request->selected)
        ->get();

    $results['observers'] = $observers;

    # get all paired results for each selected observer
    if ($request->flags['results']) {
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
      $results['inputsMeta'] = ExperimentObserverMeta::with('observer_meta')
        ->where('experiment_id', $expID)
        ->get();
    }

    # get meta data about experiment
    if ($request->flags['expMeta']) {
      $expMeta = Experiment::find($expID);

      # create array in a export ready format
      $data = [];
      $data['title']            = ['title', $expMeta->title];
      $data['experiment_type']  = ['experiment type', $expMeta->type->name];
      $data['delay']            = ['delay between stimuli switching', $expMeta->delay . 'ms'];
      $data['background_colour']= ['Background colour', '#' . $expMeta->background_colour];
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

  public function results_grouped_by_image_sets (Request $request, int $id)
  {
    $results = [];

    # get every experiment sequence of type image set, with respective images (filter out original image)
    $data = ExperimentQueue::with([
      'experiment_sequences' => function ($query) {
        $query
          ->where('experiment_sequences.picture_queue_id', '!=', NULL)
          ->with([
            'picture_set.pictures' => function ($query) {
              $query->where('is_original', 0);
            }
          ]);
      }])
      ->where('experiment_id', '=', $id)
      ->first();

    $results['imageSetSequences'] = $data->experiment_sequences;


    $matchThese = ['experiment_id' => $id];
    // exclude incomplete data?
    // if ($request->includeIncomplete == false) {
    //   $matchThese['completed'] = 1;
    // }

    $paired_results = ExperimentResult
      ::with(
        'paired_results.picture_left',
        'paired_results.picture_right',
        'paired_results.picture_selected'
      )
      ->withCount('paired_results')
      ->where($matchThese)
      ->get();

    # filter out unfinished experiments
    if ($request->includeIncomplete == false) {
      # for every picture set experiment_sequence get the count of pictures used (amount of comparisons)
      $sequences = Experiment::with([
        'sequences' => function ($query) {
          $query->where('picture_queue_id', '!=', null)->with([
            'picture_queue' => function ($query) {
              $query->withCount('picture_sequence');
            }
          ]);
        }
      ])->find($id);

      # get the total comparisons in the experiment
      $total_comparisons = $sequences->sequences->reduce(function ($carry, $item) {
        return $carry + $item->picture_queue->picture_sequence_count;
      }, 0);
      $total = $total_comparisons / 2;

      # only get the completed results (if the observer's results match total comparisons)
      $completed = $paired_results->filter(function ($value, $key) use ($total) {
        return $total == $value->paired_results_count;
      });

      $paired_results = $completed;
    }


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

        $arr['won'] = 1;

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
    # loop through every experiment sequence
    foreach ($results['imageSetSequences'] as $key => $sequence) {
      # loop through every picture in the current experiment sequence
      foreach ($sequence['picture_set']['pictures'] as $image) {
        # loop through results
        foreach ($data as $result) {
          # if the picture id in the current result matches
          # the picture id of the current picture in the current experiment sequence
          if ($result['pictureId'] == $image['id']) {
            # push the result into a new array at the sequence key position
            $new[$key][] = $result;
          }
        }
      }
      $results['imageSetSequences'][$key]['results'] = $new[$key];
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
      'picture_sequence_id'   => $request->picture_sequence,
      'client_side_timer'     => $request->client_side_timer,
      'chose_none'            => $request->chose_none
    ]);

    if ($request->artifact_marks) {
      // $shapes = [];
      foreach ($request->artifact_marks as $image) {
        foreach ($image as $mark) {
          $fill = json_encode($mark['fill']);
          // $fill = $mark['fill'];
          // array_push($shapes, $fill);
          \App\ResultImageArtifact::create([
            'experiment_result_id'  => $request->experiment_result_id,
            'picture_id'            => $mark['picture_id'],
            'selected_area'         => $fill,
            'comment'               => null,
            'client_side_timer'     => 0, // $request->client_side_timer
          ]);
        }
      }
    }

    if ($result) {
      return response('result_stored', 201);
    }
  }
}
