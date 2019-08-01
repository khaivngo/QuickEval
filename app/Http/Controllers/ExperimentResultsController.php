<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ExperimentResult;

class ExperimentResultsController extends Controller
{
    public function store (Request $request) {
      $experimentResult = ExperimentResult::create([
        'user_id' => auth()->user()->id,
        'experiment_id' => $request->experiment_id
      ]);

      if ($experimentResult) {
        return response('stored', 201);
      }
    }

    public function destroy (Request $request, Experiment $experiment) {
      // $results = $request->results;

      $userResults = auth()->user()->results->map(function ($result) {
        return $result->id;
      });

      $valid = collect($results)->every(function ($value, $key) use ($userResultIds) {
        return $userResultIds->contains($value);
      });

      if (!$valid) {
        return response()->json('Unauthorized', 401);
      }

      Result::destroy($request->results);

      return response()->json('Deleted', 200);
    }
}
