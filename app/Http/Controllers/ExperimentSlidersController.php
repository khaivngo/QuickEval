<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ExperimentSlider;

class ExperimentSlidersController extends Controller
{
  public function index ($id) {
    return
      ExperimentSlider
        ::where('experiment_id', $id)
        ->get();
  }
}
