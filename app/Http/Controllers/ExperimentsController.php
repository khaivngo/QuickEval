<?php

namespace App\Http\Controllers;

use App\Experiment;
use Illuminate\Http\Request;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)
        ->get();
    }

    public function all () {
      return Experiment::orderBy('id', 'desc')->get();
      // return Experiment::all();
    }

    public function find (Request $request) {
      // return Experiment::where('user_id', auth()->user()->id) // and id = $id
      //   ->get();
      return Experiment::find($request->id);
    }

    public function store (Request $request) {
      $data = $request->validate([
          'title' => 'required|string',
          'experimentType' => 'required'
      ]);

      $experiment = Experiment::create([
        'user_id' => auth()->user()->id,
        'title' => $request->title,
        'experiment_type' => $request->experimentType
      ]);

      return response($experiment, 201);
    }

    public function update (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
          'title' => 'required|string',
          'experiment_type' => 'required'
      ]);

      $experiment->update($data);

      return response($experiment, 200);
    }

    public function visibility (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $data = $request->validate([
          'is_public' => 'required|boolean'
      ]);

      $experiment->update($data);

      return response($experiment, 200);

      // Experiment::where('user_id', auth()->user()->id)->update($data);
    }

    public function destroy (Request $request, Experiment $experiment) {
      if ($experiment->user_id !== auth()->user()->id) {
        return response()->json('Unauthorized', 401);
      }

      $experiment->delete();

      return response('Deleted experiment', 200);
    }

    public function destroyExperimentResults (Request $request, Experiment $experiment) {
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
