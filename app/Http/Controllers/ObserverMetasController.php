<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ObserverMeta;

class ObserverMetasController extends Controller
{
    public function index () {
      return ObserverMeta
        ::where('user_id', auth()->user()->id)
        ->get();
    }
}
