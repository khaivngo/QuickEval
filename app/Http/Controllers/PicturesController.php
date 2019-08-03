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

    public function store (Request $request, PictureSet $picture_set) {
      // if ($picture_set->user_id !== auth()->user()->id) {
      //   return response()->json('Unauthorized', 401);
      // }

      $image_set_id = $request->imageSetId;
      $files = $request->file('files');

      if (! empty($files)) {
        foreach ($files as $file) {
          $path = $file->store('uploads/' . $image_set_id);
          // $path = $file->store('uploads'); // store() will automatically generate a unique file name

          $picture = Picture::create([
            // 'user_id' => auth()->user()->id,
            'name' => $path,
            'path' => $path,
            'is_original' => 0,
            'picture_set_id' => $image_set_id
          ]);
        }
      }

      return response($request, 201);
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
}
