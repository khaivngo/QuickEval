<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exports\CategoryResultsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\CategoryResult;
use App\ExperimentResult;

class CategoryResultsController extends Controller
{
    /**
     * The \Maatwebsite\Excel package is used for creating exports.
     * See: https://docs.laravel-excel.com/3.1/exports/
     * Also see files in the app/Exports folder.
     */
    public function export_observer ($id)
    {
        $experiment_results = ExperimentResult::find($id);
        $category_results = ExperimentResult
          ::with('category_results.picture', 'category_results.category')
          ->find($id)
          ->category_results;

        $data = [];
        foreach ($category_results as $result)
        {
          $arr = [];
          $arr['observer']  = $experiment_results->user_id;
          $arr['session']   = $experiment_results->id;
          $arr['picture']   = $result->picture->name;
          $arr['category']  = $result->category->title;

          array_push($data, $arr);
        }

        $filename = 'results-' . $experiment_results->user_id . '.csv';

        return Excel::download(new CategoryResultsExport($data), $filename);
    }

    public function export_all ($id) {
        // TODO: check if scientist owns experiment and that results belong to experiment

        $category_results = ExperimentResult
          ::with('category_results.picture', 'category_results.category')
          ->where('experiment_id', $id)
          ->get();

        $data = [];
        foreach ($category_results as $result) {
          foreach ($result->category_results as $res) {
            $arr = [];
            $arr['observer']  = $result->user_id;
            $arr['session']   = $result->id;
            $arr['picture']   = $res->picture->name;
            $arr['category']  = $res->category->title;
            array_push($data, $arr);
          }
        }

        $file_ext = 'csv';
        $filename = 'results.' . $file_ext;

        return Excel::download(new CategoryResultsExport($data), $filename);
    }

    public function all () {
      return CategoryResult::where('experiment_result_id', $id)->get();

      // get alle som tilhører en experiment results som har experiment_id = value
      // definerer reletationship i model... mange til mange forhold?
    }


    public function store (Request $request) {
      $result = CategoryResult::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id'           => $request->category_id,
        'picture_id_left'       => $request->picture_id_left,
        'chose_none'            => $request->chose_none
      ]);

      if ($result) {
        return response('result_stored', 201);
      }
    }
}
