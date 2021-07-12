<?php

namespace App\Http\Controllers;
use App\PictureSet;
use App\User;
use Storage;

use Illuminate\Http\Request;

class PictureSetsController extends Controller
{
    /**
     * All image sets belonging to the user.
     */
    public function index ()
    {
      # get the picture sets which the user has created
      $owned_sets = PictureSet
        ::where('user_id', auth()->user()->id)
        ->orderBy('id', 'desc')
        ->get();

      # get the picture sets which belongs to experiments the user has been invited to
      $invited_sets = User::where('id', auth()->user()->id)
        ->with('picture_sets')
        ->get()
        ->pluck('picture_sets')
        ->flatten();

      return $owned_sets->concat($invited_sets);
    }

    /**
     * Get the specified resource.
     */
    public function find ($id)
    {
      return PictureSet::with('pictures')
        ->find($id);
    }

    /**
     * Get the original image of the picture set.
     *
     * @param  int  $picture_set_id
     * @return \Illuminate\Http\Response
     */
    public function original ($picture_set_id)
    {
      return \App\Picture::where([
        ['picture_set_id', $picture_set_id],
        ['is_original', 1]
      ])->get();
    }

    /**
     * Save the specified resource to storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store (Request $request)
    {
      // $data = $request->validate([
      //     'title' => 'required|string',
      //     'user_id' => 'required'
      // ]);

      $imageSet = PictureSet::create([
        'user_id' => auth()->user()->id,
        'title' => $request->title,
        'description' => $request->description
      ]);

      return response($imageSet, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PictureSet  $set
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, PictureSet $picture_set)
    {
        // if ($picture_set->user_id !== auth()->user()->id) {
        //   return response()->json('Unauthorized', 401);
        // }

        $picture_set->title = $request->title;

        $picture_set->update();

        return response($picture_set, 200);
    }

    /**
     * Remove the specified experiment from storage, if you are the rightful owner.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id)
    {
      $picture_set = PictureSet::find($id);

      # return if not owner of image set
      if ($picture_set->user_id != auth()->user()->id) {
        return response('Unauthorized', 401);
      }

      if ($picture_set->delete()) {
        # remove the $id directory and all of its files
        Storage::deleteDirectory('public/' . $id);
      }

      return response($picture_set, 201);
    }
}
