<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

use App\Picture;
use App\PictureSet;

use Illuminate\Http\Request;

class PicturesController extends Controller
{
    public function index ($id) {
      return Picture::where('id', $id)->first();
    }

    // public function store (Request $request, PictureSet $picture_set) {
    public function store (Request $request) {
      // if ($picture_set->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $image_set_id = $request->imageSetId;
      $is_original = ((int)$request->original == 1) ? 1 : 0;
      $files = $request->file('files');

      $pics = [];
      if (!empty($files))
      {
        foreach ($files as $file)
        {
          # store() will automatically generate a unique file name
          $path = $file->store('public/' . $image_set_id);
          # add public/ add the begining of the path
          $path = str_replace('public/', "", $path);

          $picture = Picture::create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'extension' => $file->extension(),
            'is_original' => $is_original,
            'picture_set_id' => $image_set_id
          ]);
          array_push($pics, $picture);
        }
      }

      return response($pics);
    }

    protected function upload () {
      // Storage::disk('local')->put('file.jpg', 'Contents');

      // $exists = Storage::disk('local')->exists('file.jpg');

      // return Storage::download('file.jpg', 'fileNameSeenByTheDownloader', $headers);

      // The path to the file will be returned by the putFile method so you can store the path, including the generated file name, in your database.
      // Automatically generate a unique ID for file name...
      // Storage::putFile('photos', new File('/path/to/photo'));

      // // Manually specify a file name...
      // Storage::putFileAs('photos', new File('/path/to/photo'), 'photo.jpg');

      // // Storage::delete(['file.jpg', 'file2.jpg']);

      // $files = Storage::files($directory);
      // $files = Storage::allFiles($directory);

      // Storage::makeDirectory($directory);
      // Storage::deleteDirectory($directory);
    }

    /**
     * Update the avatar for the user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        // $path = $request->file('file')->store('hello.jpg');
        // $path = Storage::putFile('avatars', $request->file('avatar'));

        // return $path;
    }

    public function destroy ($id) {
      $picture = Picture::find($id);
      // $picture = Picture::destroy($id);

      # return if not owner of image set
      // if ($picture_set->user_id != auth()->user()->id) {
      //   return response('Unauthorized', 401);
      // }

      if ($picture->delete()) {
        $path = 'public/' . $picture->path;
        Storage::delete($path);
      }

      return response('deleted', 200);
    }
}
