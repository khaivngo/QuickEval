<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Result;

class ResultsController extends Controller
{
  public function store (Request $request) {
    $result = Result::create([
      'user_id' => auth()->user()->id,
      'experiment_id' => $request->experiment_id,
      'picture_order_id' => $request->picture_order_id,
      'category_id' => $request->category_id,
      'chose_none' => $request->chose_none
    ]);

    if ($result) {
      return response('result_stored', 201);
    }
  }
}
