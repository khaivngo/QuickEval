<?php

namespace App\Http\Controllers;

use App\TripletResult;
use Illuminate\Http\Request;

class TripletResultsController extends Controller
{
    public function store (Request $request) {
      $result = TripletResult::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id_left'      => $request->category_id_left,
        'category_id_middle'    => $request->category_id_middle,
        'category_id_right'     => $request->category_id_right,
        'picture_id_left'       => $request->picture_id_left,
        'picture_id_middle'     => $request->picture_id_middle,
        'picture_id_right'      => $request->picture_id_right,
        'chose_none'            => $request->chose_none
      ]);

      if ($result) {
        return response('result_stored', 201);
      }
    }
}
