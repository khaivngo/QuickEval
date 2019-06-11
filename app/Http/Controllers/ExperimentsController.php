<?php

namespace App\Http\Controllers;

use App\Experiment;
use Illuminate\Http\Request;

class ExperimentsController extends Controller
{
    public function index () {
      return Experiment::where('user_id', auth()->user()->id)
        ->get();
    }

    public function store (Request $request) {
      $data = $request->validate([
          'title' => 'required|string',
          'user_id' => 'required',
          'experiment_type' => 'required'
      ]);

      $experiment = Experiment::create($data);
    }
}
