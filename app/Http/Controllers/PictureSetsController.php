<?php

namespace App\Http\Controllers;
use App\PictureSet;
use Storage;

use Illuminate\Http\Request;

class PictureSetsController extends Controller
{
    /**
     * All image sets belonging to the user.
     */
    public function index () {
      return PictureSet
        ::where('user_id', auth()->user()->id)
        ->orderBy('id', 'desc')
        ->get();
    }

    /**
     * All image sets in the whole database.
     */
    public function all () {
      return PictureSet
        ::orderBy('id', 'desc')
        ->get();
    }

    public function find ($id) {
      return PictureSet
        ::with([
          'pictures' => function ($query) { $query->orderBy('order_index', 'asc'); },
          'experiment_sequences.experiment_queue.experiment'
        ])->find($id);
    }

    public function original ($picture_set_id) {
      return \App\Picture::where([
        ['picture_set_id', $picture_set_id],
        ['is_original', 1]
      ])->get();
    }

    public function store (Request $request) {
      if (auth()->user()->role < 2) {
        return response()->json('Unauthorized', 401);
      }
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

    public function move (Request $request)
    {
        $left  = \App\Picture::find($request[0]);
        $right = \App\Picture::find($request[1]);
        $moved = \App\Picture::find($request[2]);

        if (!$left) { # first position
          $new_index = round( (0 + $right->order_index) / 2);
          if ($new_index >= $right->order_index) {
            $pics = PictureSet
              ::with(['pictures' => function ($query) { $query->orderBy('order_index', 'asc'); }])
              ->find($moved->picture_set_id);

            $modifier = 1;
            foreach ($pics->pictures as $pic) {
              $order_index = ($modifier * 1024);
              $modifier = $modifier + 1;

              $pic->order_index = $order_index;
              $pic->save();
            }
          } else {
            $moved->order_index = $new_index;
          }
        }
        else if (!$right) { # last position
          $new_index = round( (($left->order_index + 1024) + $left->order_index) / 2);
          if ($new_index <= $left->order_index) {
            $pics = PictureSet
              ::with(['pictures' => function ($query) { $query->orderBy('order_index', 'asc'); }])
              ->find($moved->picture_set_id);

            $modifier = 1;
            foreach ($pics->pictures as $pic) {
              $order_index = ($modifier * 1024);
              $modifier = $modifier + 1;

              $pic->order_index = $order_index;
              $pic->save();
            }
          }
          $moved->order_index = $new_index;
        }
        else {
          $new_index = round( ($left->order_index + $right->order_index) / 2);
          if ($new_index >= $right->order_index || $new_index <= $left->order_index) {
            $pics = PictureSet
              ::with(['pictures' => function ($query) { $query->orderBy('order_index', 'asc'); }])
              ->find($moved->picture_set_id);

            $modifier = 1;
            foreach ($pics->pictures as $pic) {
              $order_index = ($modifier * 1024);
              $modifier = $modifier + 1;

              $pic->order_index = $order_index;
              $pic->save();
            }
          }
          $moved->order_index = $new_index;
        }

        $moved->save();

        return response($moved, 200);
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
