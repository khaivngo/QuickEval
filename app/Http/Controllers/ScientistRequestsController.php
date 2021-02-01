<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ScientistRequest;
use App\User;

class ScientistRequestsController extends Controller
{
  public function index ()
  {
    if (auth()->user()->role != 3) {
      return response()->json('Unauthorized', 401);
    }

    $requests = \App\ScientistRequest
      ::where('accepted', 0)
      ->get();

    $all = [];
    foreach ($requests as $request) {
      array_push($all, $request->user);
    }

    return response($all, 200);
  }

  /**
   *
   */
  public function accept ($id)
  {
    if (auth()->user()->role != 3) {
      return response()->json('Unauthorized', 401);
    }

    User::where('id', $id)->update(['role' => 2]);
    ScientistRequest::where('user_id', $id)->update(['accepted' => 1]);

    // send email to marius.pedersen@ntnu.no

    return response('permissions updated', 200);
  }

  public function reject ($id)
  {
    if (auth()->user()->role != 3) {
      return response()->json('Unauthorized', 401);
    }

    ScientistRequest::where('user_id', $id)->delete();

    return response('deleted', 200);
  }
}
