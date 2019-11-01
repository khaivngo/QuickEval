<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use DB;

use App\ExperimentResult;

class ExperimentResultsController extends Controller
{
    /**
     *
     */
    public function index ($experiment_id) {
      return \App\Experiment::find($experiment_id)
        ->results()
        ->get();
    }

    public function fetch ($id) {
      return \App\ExperimentResult
        ::where('experiment_id', $id)
        ->get();
    }

    public function store (Request $request) {
      $experimentResult = ExperimentResult::create([
        'user_id' => auth()->user()->id,
        'experiment_id' => $request->experimentId,
        'start_time' => time() // or microtime()
      ]);

      if ($experimentResult) {
        return response($experimentResult, 201);
      }
    }

    public function destroy ($id) {
      // check that scientist owns experiment

      $results = \App\ExperimentResult::where('experiment_id', $id)->get();

      $experiment_results = $results->map(function ($result) {
        return $result->id;
      });

      \App\ExperimentResult::destroy($experiment_results);

      return response()->json('deleted', 200);
    }

    // public function destroy (Request $request, Experiment $experiment) {
    //   // $results = $request->results;

    //   $userResults = auth()->user()->results->map(function ($result) {
    //     return $result->id;
    //   });

    //   $valid = collect($results)->every(function ($value, $key) use ($userResultIds) {
    //     return $userResultIds->contains($value);
    //   });

    //   if (!$valid) {
    //     return response()->json('Unauthorized', 401);
    //   }

    //   Result::destroy($request->results);

    //   return response()->json('Deleted', 200);
    // }
}
