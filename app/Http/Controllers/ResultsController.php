<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Result;

class ResultsController extends Controller
{
  public function store (Request $request) {
    $result = Result::create([
      'user_id' => auth()->user()->id, // this can be removed now that we have experiment_result_id
      'experiment_id' => $request->experiment_id, // this can be removed now that we have experiment_result_id
      'experiment_result_id' => $request->experiment_result_id,
      'picture_order_id' => $request->picture_order_id,
      'category_id' => $request->category_id,
      'chose_none' => $request->chose_none
    ]);

    if ($result) {
      return response('result_stored', 201);
    }
  }

  public function indexOld ($id) {
    return Result::where('experiment_result_id', $id)->get();

    $result = Result
      ::join('experiment_queues',    'experiment_queues.experiment_id',       '=', 'experiments.id')
      ->join('experiment_sequences', 'experiment_queues.id',                  '=', 'experiment_sequences.experiment_queue_id')
      ->join('picture_queues',       'experiment_sequences.picture_queue_id', '=', 'picture_queues.id')
      ->join('picture_sequences',    'picture_queues.id',                     '=', 'picture_sequences.picture_queue_id')
      ->join('pictures',             'picture_sequences.picture_id',          '=', 'pictures.id')
      ->join('picture_sets',         'pictures.picture_set_id',               '=', 'picture_sets.id')
      ->select('users.id',  'contacts.phone', 'orders.price')
      ->where ('experiment_result_id', $id)
      ->get();

      // getQuery()

      // get one sequence and join in image names

      //


    return $results;

    // interesting data:
      // - data for each image
  }

  /**
   *
   */
  public function index ($id)
  {
    # get one observer's selected pictures for a specific experiment
    $results = Result::join('picture_sequences', 'results.picture_order_id', '=', 'picture_sequences.id')
      ->join('pictures', 'picture_sequences.picture_id', '=', 'pictures.id')
      ->where('experiment_result_id', $id)
      ->get();

    return $results;

    // $sequences = DB::table('experiment_queues')
    //   ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
    //   ->where('experiment_queues.experiment_id', $id)
    //   ->get();

    // $all = [];
    // foreach ($sequences as $sequence)
    // {
    //   if ($sequence->picture_queue_id !== null)
    //   {
    //     # get all picture sequences
    //     $result = DB::table('experiment_sequences')
    //       ->join('picture_sequences', 'picture_sequences.picture_queue_id', '=', 'experiment_sequences.picture_queue_id')
    //       ->where('experiment_sequences.id', $sequence->id)
    //       ->get();

    //     array_push($all, $result);
    //   }
    // }

    // return response($all);
  }
}
