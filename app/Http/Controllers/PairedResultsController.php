<?php

namespace App\Http\Controllers;

use App\Exports\PairedResultsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

use App\PairedResult;

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
      $experiment_results = \App\ExperimentResult::find($id);
      $paired_results = \App\ExperimentResult::find($id)->paired_results;

      // TODO: get rid of the loop
      $data = [];
      foreach ($paired_results as $result)
      {
        $arr = [];
        $arr['observer'] = $experiment_results->user_id;
        $arr['left']     = $result->picture_left->name;
        $arr['right']    = $result->picture_right->name;
        $arr['selected'] = $result->picture_selected->name;
        array_push($data, $arr);
      }

      $file_ext = 'csv';
      // TODO: pre/append user_id or created_at to filename
      $filename = 'results.' . $file_ext;

      return Excel::download(new PairedResultsExport($data), $filename);
  }

  public function export_all ($id) {
    // TODO: check if scientist owns experiment and that results belong to experiment

    // return \App\PairedResult
    //   ::where('experiment_result_id', $id)
    //   ->get();

      $paired_results = \App\ExperimentResult::where('experiment_id', $id)->get();
      // return $paired_results;

      $data = [];
      foreach ($paired_results as $result) {
        foreach ($result->paired_results as $res) {
          $arr = [];
          $arr['observer']  = $result->user_id;
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
