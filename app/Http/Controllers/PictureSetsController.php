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
      # the image that is on the left side of the new position of the moved image
      $nextElIndexNumber = \App\Picture::find($request[0]);
      # the image that is on the right side of the new position of the moved image
      $prevElIndexNumber = \App\Picture::find($request[1]);
      # image that has been moved
      $moved = \App\Picture::find($request[2]);

      // Stimuli groups uploaded before the drag-and-drop feature was implemented will not have a order_index,
      // so if the user tries to rearrange one of these groups then we generate a new queue for every
      // stimuli in the group
      if ($moved->order_index === null) {
        $pics = PictureSet::with([
          'pictures' => function ($query) { $query->orderBy('order_index', 'asc'); }
        ])->find($moved->picture_set_id);

        $modifier = 1;
        foreach ($pics->pictures as $pic) {
          $order_index = ($modifier * 1024);
          $modifier++;

          $pic->order_index = $order_index;
          $pic->save();
        }

        $right = $pics->pictures->find( $prevElIndexNumber->id );
        $left  = $pics->pictures->find( $nextElIndexNumber->id );
        $new_index = floor(($left->order_index + $right->order_index) / 2);
        $moved->order_index = $new_index;
        $moved->save();

        return 'New order queue generated.';
      }

      # When there is no left image
      if (!$nextElIndexNumber) {
        // $new_index = $prevElIndexNumber->order_index - 512;
        $new_index = floor( (0 + $prevElIndexNumber->order_index) / 2 );
      }
      # When there is no right image
      else if (!$prevElIndexNumber) {
        $new_index = $nextElIndexNumber->order_index + 512;
        // $new_index = $nextElIndexNumber->order_index + 1024;
        // $new_index = floor( (($nextElIndexNumber->order_index + 1024) + $nextElIndexNumber->order_index) / 2 );
      }
      # If there are images on both the left and right side of the dragged-and-dropped image
      else {
        $new_index = floor(($prevElIndexNumber->order_index + $nextElIndexNumber->order_index) / 2);
      }

      $moved->order_index = $new_index;
      $moved->save();


      # After moving many times two indexes willl overlap, when that happens we need to reorder every index
      if (isset($prevElIndexNumber->order_index)) {
        if (
          abs($moved->order_index - $prevElIndexNumber->order_index) <= 3
        ) {
          $pics = PictureSet::with([
            'pictures' => function ($query) { $query->orderBy('order_index', 'asc'); }
          ])->find($moved->picture_set_id);
  
          $modifier = 1;
          foreach ($pics->pictures as $pic) {
            $order_index = ($modifier * 1024);
            $modifier = $modifier + 1;
            // $modifier++;
  
            $pic->order_index = $order_index;
            $pic->save();
          }

          // $right = $pics->pictures->find( $prevElIndexNumber->id );
          // $left  = $pics->pictures->find( $nextElIndexNumber->id );
          // $new_index = floor(($left->order_index + $right->order_index) / 2);
          // $moved->order_index = $new_index;
          // $moved->save();
        }
      }

      if (isset($nextElIndexNumber->order_index)) {
        if (
          abs($moved->order_index - $nextElIndexNumber->order_index) <= 3
        ) {
          $pics = PictureSet::with([
            'pictures' => function ($query) { $query->orderBy('order_index', 'asc'); }
          ])->find($moved->picture_set_id);
  
          $modifier = 1;
          foreach ($pics->pictures as $pic) {
            $order_index = ($modifier * 1024);
            $modifier = $modifier + 1;
            // $modifier++;
  
            $pic->order_index = $order_index;
            $pic->save();
          }
        }
      }


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
