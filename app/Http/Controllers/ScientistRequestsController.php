<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    # update the user role to scientist
    User::where('id', $id)->update(['role' => 2]);

    # tag the scientist request as accepted
    ScientistRequest::where('user_id', $id)->update(['accepted' => 1]);

    # send mail to the person being accepted
    $user = User::find($id);
    Mail::to($user->email)->send(new \App\Mail\AcceptedRequest());

    return response('permissions updated', 200);
  }

  /**
   *
   */
  public function reject ($id)
  {
    if (auth()->user()->role != 3) {
      return response()->json('Unauthorized', 401);
    }

    ScientistRequest::where('user_id', $id)->delete();

    // try {
    //   // send mail to the person being rejected
    //   Mail::to('robin.vigdal.bekkevold@gmail.com')->send(new \App\Mail\Rejected());
    // } catch (Exception $ex) {}

    return response('deleted', 200);
  }
}
