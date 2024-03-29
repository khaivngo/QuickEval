<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends Controller
{
    /**
     * Return the logged in user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function you(Request $request)
    {
      return $request->user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      # abort if not admin
      if (auth()->user()->role < 3) {
        return response()->json('Unauthorized', 401);
      }

      return User::where('role', '>', 1)
        ->orderBy('id', 'desc')
        ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search($term)
    {
        // with('user:id,name', 'observer_metas.observer_meta')
        return User::where([
            ['role', '>', 1],
            ['name', 'LIKE', '%'.$term.'%']
        ])
        // LIMIT?
        // ->orderBy('id', 'desc')
        ->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);

        if (Hash::check($request->oldPassword, $user->password))
        {
            $user->password = Hash::make($request->newPassword);
            $user->save();

            return response('Password successfully updated.', 200);
        }

        return response('Your credentials are incorrect. Please try again.', 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request)
    {
        # abort if not admin
        if (auth()->user()->role < 3) {
            return response()->json('Unauthorized', 401);
        }

        $user = User::find($request->id);

        $user->role = $request->role;
        $user->save();

        return response('User role successfully updated.', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy ()
    {
      // if ($user->id != auth()->user()->id) {
      //   return response('Unauthorized', 401);
      // }
      $user = User::find(auth()->user()->id);

      // delete images, experiments? if owner. Remove collaborator statuses.

      $user->delete();

      return response($user, 200);
    }
}
