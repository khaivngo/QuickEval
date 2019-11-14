<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExperimentObserverMetaResult;
use DB;

use App\Exports\ExperimentObserverMetaResultsExport;
use Maatwebsite\Excel\Facades\Excel;

class ExperimentObserverMetaResultsController extends Controller
{
    public function store (Request $request) {
      $observerInputs = [];

      foreach ($request->all() as $observerInput) {
        $answer = isset($observerInput['answer']) ? $observerInput['answer'] : null;

        array_push($observerInputs, [
          'user_id'          => auth()->user()->id,
          'answer'           => $answer,
          'experiment_id'    => $observerInput['experiment_id'],
          'observer_meta_id' => $observerInput['observer_meta_id']
        ]);
      }

      ExperimentObserverMetaResult::insert($observerInputs);

      return response('inserted', 201);
    }

    /**
     * Get one observers observer_meta answers for a experiment.
     */
    public function index ($experiment_id, $user_id) {
      // if ($experiment->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $experiment_observer_meta_results = DB::table('experiment_observer_meta_results')
        ->join('observer_metas', 'experiment_observer_meta_results.observer_meta_id', '=', 'observer_metas.id')
        ->where([
          ['experiment_observer_meta_results.experiment_id', $experiment_id],
          ['experiment_observer_meta_results.user_id', $user_id]
        ])->get();

      return response($experiment_observer_meta_results, 200);
    }

    /**
     * Simple test to see if the experiment has any observer inputs.
     * This lets us determine if we should display buttons to export observer input answers,
     * without having to fetch everything.
     */
    public function find_or_fail ($experiment_id) {
      return ExperimentObserverMetaResult::where('experiment_id', $experiment_id)->first();
    }

    /**
     * Get all observer_meta answers belonging to a experiment.
     */
    public function index_all ($experiment_id) {
      // if experiment is owned by researcher
      // if ($experiment->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $experiment_observer_meta_results = DB::table('experiment_observer_meta_results')
        ->join('observer_metas', 'experiment_observer_meta_results.observer_meta_id', '=', 'observer_metas.id')
        ->where('experiment_observer_meta_results.experiment_id', $experiment_id)
        ->get([
          'experiment_observer_meta_results.user_id',
          'observer_metas.meta',
          'experiment_observer_meta_results.answer'
        ]);

      return response($experiment_observer_meta_results, 200);
    }

    public function export_all ($experiment_id) {
      // if experiment is owned by researcher
      // if ($experiment->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $experiment_observer_meta_results = DB::table('experiment_observer_meta_results')
        ->join('observer_metas', 'experiment_observer_meta_results.observer_meta_id', '=', 'observer_metas.id')
        ->where('experiment_observer_meta_results.experiment_id', $experiment_id)
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

      $file_ext = 'csv';
      $filename = 'input-answers.csv';

      return Excel::download(new ExperimentObserverMetaResultsExport($data), $filename);
    }

    public function export_observer ($experiment_id, $user_id) {
      // if ($experiment->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $experiment_observer_meta_results = DB::table('experiment_observer_meta_results')
        ->join('observer_metas', 'experiment_observer_meta_results.observer_meta_id', '=', 'observer_metas.id')
        ->where([
          ['experiment_observer_meta_results.experiment_id', $experiment_id],
          ['experiment_observer_meta_results.user_id', $user_id]
        ])->get([
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

      $file_ext = 'csv';
      $filename = 'input-answers-' . $user_id . '.csv';

      return Excel::download(new ExperimentObserverMetaResultsExport($data), $filename);
    }
}
