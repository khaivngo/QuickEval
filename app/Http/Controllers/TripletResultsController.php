<?php

namespace App\Http\Controllers;

use App\Exports\TripletResultsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

use App\TripletResult;
use App\ExperimentResult;

class TripletResultsController extends Controller
{
    /**
     * The \Maatwebsite\Excel package is used for creating exports.
     * See: https://docs.laravel-excel.com/3.1/exports/
     * Also see files in the app/Exports folder.
     */
    public function export_observer (int $id)
    {
        $experiment_results = ExperimentResult::find($id);
        $triplet_results = ExperimentResult
          ::with(
            'triplet_results.picture_left', 'triplet_results.picture_middle', 'triplet_results.picture_right',
            'triplet_results.category_left', 'triplet_results.category_middle', 'triplet_results.category_right'
          )
          ->find($id)
          ->triplet_results;

        // TODO: get rid of the loop
        $data = [];
        foreach ($triplet_results as $result)
        {
          $arr = [];
          $arr['observer'] = $experiment_results->user_id;
          $arr['session']  = $experiment_results->id;

          $arr['picture_left']   = $result->picture_left->name;
          $arr['picture_middle'] = $result->picture_middle->name;
          $arr['picture_right']  = $result->picture_right->name;

          $arr['category_left']   = $result->category_left->title;
          $arr['category_middle'] = $result->category_middle->title;
          $arr['category_right']  = $result->category_right->title;

          array_push($data, $arr);
        }

        $file_ext = 'csv';
        // TODO: pre/append user_id or created_at to filename
        $filename = 'results-' . $experiment_results->user_id . '.csv';

        return Excel::download(new TripletResultsExport($data), $filename);
    }

    public function export_all (int $id) {
        // TODO: check if scientist owns experiment and that results belong to experiment

        $triplet_results = ExperimentResult
          ::with(
            'triplet_results.picture_left', 'triplet_results.picture_middle', 'triplet_results.picture_right',
            'triplet_results.category_left', 'triplet_results.category_middle', 'triplet_results.category_right'
          )
          ->where('experiment_id', $id)
          ->get();

        $data = [];
        foreach ($triplet_results as $result) {
          foreach ($result->triplet_results as $res) {
            $arr = [];
            $arr['observer']       = $result->user_id;
            $arr['session']        = $result->id;
            $arr['picture_left']   = $res->picture_left->name;
            $arr['picture_middle'] = $res->picture_middle->name;
            $arr['picture_right']  = $res->picture_right->name;

            $arr['category_left']   = $res->category_left->title;
            $arr['category_middle'] = $res->category_middle->title;
            $arr['category_right']  = $res->category_right->title;

            array_push($data, $arr);
          }
        }

        $file_ext = 'csv';
        $filename = 'results.' . $file_ext;

        return Excel::download(new TripletResultsExport($data), $filename);
    }

    public function all () {
      return TripletResult::where('experiment_result_id', $id)->get();

      // get alle som tilhører en experiment results som har experiment_id = value
      // definerer reletationship i model... mange til mange forhold?
    }

    public function store (Request $request) {
      $result = TripletResult::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id_left'      => $request->category_id_left,
        'category_id_middle'    => $request->category_id_middle,
        'category_id_right'     => $request->category_id_right,
        'picture_id_left'       => $request->picture_id_left,
        'picture_id_middle'     => $request->picture_id_middle,
        'picture_id_right'      => $request->picture_id_right,
        'chose_none'            => $request->chose_none
      ]);

      return response('result_stored', 201);
    }
}
