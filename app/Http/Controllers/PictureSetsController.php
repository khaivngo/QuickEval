<?php

namespace App\Http\Controllers;
use App\PictureSet;
use Storage;

use Illuminate\Http\Request;

class PictureSetsController extends Controller
{
    public function index () {
      return PictureSet
        ::where('user_id', auth()->user()->id)
        ->orderBy('id', 'desc')
        ->get();
    }

    public function all () {
      return PictureSet::orderBy('id', 'desc')
        ->get();
    }

    public function pictures ($picture_set_id) {
      return \App\Picture::where('picture_set_id', $picture_set_id)->get();
    }

    public function original ($picture_set_id) {
      return \App\Picture::where([
        ['picture_set_id', $picture_set_id],
        ['is_original', 1]
      ])->get();
    }

    public function store (Request $request) {
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

    public function destroy ($id) {
      $picture_set = PictureSet::find($id);

      if ($picture_set->delete()) {
        # deleteDirectory: remove the $id directory and all of its files
        Storage::deleteDirectory('uploads/' . $id);
      }

      return response('deleted_picture_set', 201);
    }
}
