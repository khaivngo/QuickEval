<?php

namespace App\Http\Controllers;

use App\CategoryResult;
use Illuminate\Http\Request;

class CategoryResultsController extends Controller
{
    public function store (Request $request) {
      $result = CategoryResult::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id'           => $request->category_id,
        'picture_id_left'       => $request->picture_id_left,
        'chose_none'            => $request->chose_none
      ]);

      if ($result) {
        return response('result_stored', 201);
      }
    }
}
