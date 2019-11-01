<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;

class ExperimentObserverMetasController extends Controller
{
    public function index($experiment_id)
    {
      $sequences = DB::table('experiment_observer_metas')
        ->join('observer_metas', 'experiment_observer_metas.observer_meta_id', '=', 'observer_metas.id')
        ->where('experiment_observer_metas.experiment_id', $experiment_id)
        ->get();

      return response($sequences, 200);
    }
}
