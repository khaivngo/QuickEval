<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use DB;

use App\Experiment;
use App\ExperimentResult;

class ExperimentResultsController extends Controller
{
    public function index ($experiment_id) {
      return \App\Experiment::find($experiment_id)
        ->results(); // replace with ::with('')
        // ->get();
    }

    public function fetch (Experiment $experiment)
    {
      if ($experiment->type->slug == 'rank-order') {
        $slug = 'rank_order';
      } else {
        $slug = $experiment->type->slug;
      }

      return ExperimentResult
        ::with('user')
        ->withCount($slug . '_results')
        ->where('experiment_id', $experiment->id)
        ->get();
    }

    public function store (Request $request)
    {
      $experimentResult = ExperimentResult::create([
        'user_id' => auth()->user()->id,
        'experiment_id' => $request->experimentId,
        'start_time' => time()
      ]);

      if ($experimentResult) {
        return response($experimentResult, 201);
      }
    }

    public function completed (ExperimentResult $result) {

      // TODO: Why does this fail?!
      // if ($result->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      // $data = $request->validate([
      //   'is_public' => 'required'
      // ]);

      $result->update([
        'completed' => 1,
        'end_time' => time()
      ]);

      return response($result, 200);
    }

    public function update (Request $request, ExperimentResult $result)
    {
      $result->update($request->all());
      return response($result, 200);
    }

    public function destroy (Request $request) {
      // TODO: check if owner

      $deleted = ExperimentResult::destroy($request->selected);

      return response()->json($request, 200);
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
