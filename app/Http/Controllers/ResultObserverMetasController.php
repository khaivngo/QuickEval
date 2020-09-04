<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ResultObserverMeta;

class ResultObserverMetasController extends Controller
{
    public function store (Request $request)
    {
      $observerInputs = [];

      foreach ($request->inputs as $observerInput) {
        $answer = isset($observerInput['observer_meta']['answer']) ?
          $observerInput['observer_meta']['answer'] : null;

        array_push($observerInputs, [
          'experiment_result_id' => $request->resultObserverMetaId,
          'answer'               => $answer,
          'observer_meta_id'     => $observerInput['observer_meta_id']
        ]);
      }

      ResultObserverMeta::insert($observerInputs);

      return response('inserted', 201);
    }
}
