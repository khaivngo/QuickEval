<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ExperimentResult;

class ResultImageArtifactsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        // $matchThese = ['experiment_id' => $id];
        // // exclude incomplete data?
        // if ($request->includeIncomplete == false) {
        //   $matchThese['completed'] = 1;
        // }

        $artifacts = ExperimentResult::with('image_artifact_results.picture')
          // ->where($matchThese)
          ->where('experiment_id', $id)
          ->get();
        // return $artifacts;

        $merged_artifacts = [];
        foreach ($artifacts as $artifact) {
          array_push($merged_artifacts, $artifact->image_artifact_results);
        }

        $collected = collect($merged_artifacts)->flatten();
        $results = $collected->groupBy('picture_id');

        return $results;
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
    public function show ($id) {
      return ExperimentResult::with('image_artifact_results')
        ->where('id', $id)
        ->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
