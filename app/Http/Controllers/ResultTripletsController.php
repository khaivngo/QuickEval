<?php

namespace App\Http\Controllers;

use App\Exports\ResultTripletsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

use App\ResultTriplet;
use App\ExperimentResult;

class ResultTripletsController extends Controller
{
    /**
     * Export a listing of the resource in a CSV file.
     *
     * @return \Maatwebsite\Excel\Facades\Excel
     */
    public function export (Request $request) {
      // TODO: check if scientist owns experiment and that results belong to experiment

      # get all triplet results for each observer
      $observers = ExperimentResult
        ::with(
          'triplet_results.picture_left', 'triplet_results.picture_middle', 'triplet_results.picture_right',
          'triplet_results.category_left', 'triplet_results.category_middle', 'triplet_results.category_right'
        )
        ->whereIn('id', $request->selected)
        ->get();

      # construct and array with result data for exporting
      $data = [];
      foreach ($observers as $observer) {
        foreach ($observer->triplet_results as $key => $result) {
          $arr = [];
          $arr['observer']       = $observer->user_id;
          $arr['session']        = $observer->id;
          $arr['picture_left']   = $result->picture_left->name;
          $arr['picture_middle'] = $result->picture_middle->name;
          $arr['picture_right']  = $result->picture_right->name;

          $arr['category_left']   = $result->category_left->title;
          $arr['category_middle'] = $result->category_middle->title;
          $arr['category_right']  = $result->category_right->title;

          $arr['time_spent']      = $result->client_side_timer;

          array_push($data, $arr);
        }
      }

      $file_ext = 'csv';
      // TODO: pre/append user_id or created_at to filename
      $filename = 'results.' . $file_ext;

      # see: https://docs.laravel-excel.com/3.1/exports/
      return Excel::download(new ResultTripletsExport($data), $filename);
    }


    public function all () {
      // get alle som tilhører en experiment results som har experiment_id = value
      // definerer reletationship i model... mange til mange forhold?
      return ResultTriplet::where('experiment_result_id', $id)->get();
    }

    public function store (Request $request) {
      $result = ResultTriplet::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id_left'      => $request->category_id_left,
        'category_id_middle'    => $request->category_id_middle,
        'category_id_right'     => $request->category_id_right,
        'picture_id_left'       => $request->picture_id_left,
        'picture_id_middle'     => $request->picture_id_middle,
        'picture_id_right'      => $request->picture_id_right,
        'chose_none'            => $request->chose_none,
        'client_side_timer'     => $request->client_side_timer
      ]);

      return response('result_stored', 201);
    }
}
