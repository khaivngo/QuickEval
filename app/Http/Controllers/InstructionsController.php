<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Instruction;

class InstructionsController extends Controller
{
    public function index () {
      return Instruction::where('user_id', auth()->user()->id)->get();
    }
}
