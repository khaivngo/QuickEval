<?php

namespace App\Http\Controllers;

use App\Exports\PairedResultsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

use App\PairedResult;
use App\ExperimentResult;

use DB;

class PairedResultsController extends Controller
{
  public function index ($id) {
    // return \App\PairedResult::where('experiment_result_id', $id)->get();

    $paired_results = \App\ExperimentResult
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

        // ->picture_selected
        // ->picture_selected_left
        // ->picture_selected_right;
      // ->pictures
      // ->join(
      //   'paired_results.picture_id_selected',
      //   '=',
      //   'pictures', 'pictures.id'
      // );
  }

  /**
   * The \Maatwebsite\Excel package is used for creating exports.
   * See: https://docs.laravel-excel.com/3.1/exports/
   * Also see files in the app/Exports folder.
   */
  public function export_observer ($id)
  {
      $experiment_results = ExperimentResult::find($id);
      $paired_results = ExperimentResult
        ::with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected')
        ->find($id)
        ->paired_results;

      $data = [];
      foreach ($paired_results as $result)
      {
        $arr = [];
        $arr['observer'] = $experiment_results->user_id;
        $arr['session']  = $experiment_results->id;
        $arr['left']     = $result->picture_left->name;
        $arr['right']    = $result->picture_right->name;
        $arr['selected'] = $result->picture_selected->name;
        array_push($data, $arr);
      }

      $file_ext = 'csv';
      $filename = 'results-' . $experiment_results->user_id . '.csv';

      return Excel::download(new PairedResultsExport($data), $filename);
  }

  public function export_all ($id) {
      // TODO: check if scientist owns experiment and that results belong to experiment

      // return \App\PairedResult
      //   ::where('experiment_result_id', $id)
      //   ->get();

      $paired_results = ExperimentResult
        ::with('paired_results.picture_left', 'paired_results.picture_right', 'paired_results.picture_selected')
        ->where('experiment_id', $id)
        ->get();

      $data = [];
      foreach ($paired_results as $result) {
        foreach ($result->paired_results as $res) {
          $arr = [];
          $arr['observer']  = $result->user_id;
          $arr['session']   = $result->id;
          $arr['left']      = $res->picture_left->name;
          $arr['right']     = $res->picture_right->name;
          $arr['selected']  = $res->picture_selected->name;
          array_push($data, $arr);
        }
      }

      $file_ext = 'csv';
      // TODO: pre/append user_id or created_at to filename
      $filename = 'results.' . $file_ext;

      return Excel::download(new PairedResultsExport($data), $filename);
  }

  public function all () {
    return PairedResult::where('experiment_result_id', $id)->get();

    // get alle som tilhører en experiment results som har experiment_id = value
    // definerer reletationship i model... mange til mange forhold?
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


  /**
   *
   */
  public function store (Request $request) {
    $result = PairedResult::create([
      'experiment_result_id'  => $request->experiment_result_id,
      'picture_id_selected'   => $request->picture_id_selected,
      'picture_id_left'       => $request->picture_id_left,
      'picture_id_right'      => $request->picture_id_right,
      'chose_none'            => $request->chose_none
    ]);

    if ($result) {
      return response('result_stored', 201);
    }
  }
}
