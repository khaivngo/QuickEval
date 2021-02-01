<?php

namespace App\Http\Controllers;

use DB;

use Illuminate\Http\Request;

class ExperimentSequencesController extends Controller
{
    public function index($experiment_id) {
      $sequences = DB::table('experiment_queues')
        ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
        ->leftJoin('picture_sets', 'experiment_sequences.picture_set_id', '=', 'picture_sets.id')
        ->leftJoin('instructions', 'experiment_sequences.instruction_id', '=', 'instructions.id')
        ->where('experiment_queues.experiment_id', $experiment_id)
        ->get();

      return response($sequences, 200);
    }
}
