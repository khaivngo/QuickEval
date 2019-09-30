<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PairedResultsController extends Controller
{
  public function store (Request $request) {
    $result = \App\PairedResult::create([
      'experiment_result_id'      => $request->experiment_result_id,
      'picture_order_id_selected' => $request->picture_order_id_selected,
      'picture_order_id_left'     => $request->picture_order_id_left,
      'picture_order_id_right'    => $request->picture_order_id_right,
      'chose_none'                => $request->chose_none
    ]);

    if ($result) {
      return response('result_stored', 201);
    }
  }
}
