<?php

namespace App\Http\Controllers;
use App\PictureSet;

use Illuminate\Http\Request;

class PictureSetsController extends Controller
{
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

      return response($imageSet, 201); // JSON
    }
}
