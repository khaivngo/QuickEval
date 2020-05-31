<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exports\ResultCategoriesExport;
use Maatwebsite\Excel\Facades\Excel;

use App\ResultCategory;
use App\ExperimentResult;

class ResultCategoriesController extends Controller
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
        ::with('category_results.picture', 'category_results.category')
        ->whereIn('id', $request->selected)
        ->get();

      # construct and array with result data for exporting
      $data = [];
      foreach ($observers as $observer) {
        foreach ($observer->category_results as $key => $result) {
          $arr = [];
          $arr['observer']   = $observer->user_id;
          $arr['session']    = $observer->id;
          $arr['picture']    = $result->picture->name;
          $arr['category']   = $result->category->title;
          $arr['time_spent'] = $result->client_side_timer;

          array_push($data, $arr);
        }
      }

      $file_ext = 'csv';
      // TODO: pre/append user_id or created_at to filename
      $filename = 'results.' . $file_ext;

      # see: https://docs.laravel-excel.com/3.1/exports/
      return Excel::download(new ResultCategoriesExport($data), $filename);
    }


    public function store (Request $request) {
      $result = ResultCategory::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id'           => $request->category_id,
        'picture_id_left'       => $request->picture_id_left,
        'chose_none'            => $request->chose_none,
        'client_side_timer'     => $request->client_side_timer
      ]);

      if ($result) {
        return response('result_stored', 201);
      }
    }
}
