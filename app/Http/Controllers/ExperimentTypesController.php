<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExperimentTypesController extends Controller
{
    public function all () {
      return \App\ExperimentType::orderBy('id', 'asc')->get();
    }
}
