<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExperimentCategoriesController extends Controller
{
  public function index ($id)
  {
    $experiment_categories = \App\ExperimentCategory::where('experiment_id', $id)->get();

    $all = [];
    foreach ($experiment_categories as $experiment_category) {
      array_push($all, $experiment_category->category);
    }

    return response($all, 200);
  }
}
