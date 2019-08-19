<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExperimentObserverMetaResult;

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
}
