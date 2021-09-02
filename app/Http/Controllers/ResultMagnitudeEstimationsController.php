<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResultMagnitudeEstimationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $result = \App\ResultMagnitudeEstimation::create([
            'experiment_result_id'  => $request->experiment_result_id,
            'magnitude_value'       => $request->magnitude_value,
            'picture_id_left'       => $request->picture_id_left,
            'chose_none'            => $request->chose_none,
            'client_side_timer'     => $request->client_side_timer
        ]);

        if ($request->artifact_marks) {
            foreach ($request->artifact_marks as $image) {
                foreach ($image as $mark) {
                $fill = json_encode($mark['fill']);
                \App\ResultImageArtifact::create([
                    'experiment_result_id'  => $request->experiment_result_id,
                    'picture_id'            => $mark['picture_id'],
                    'selected_area'         => $fill,
                    'comment'               => null,
                    'client_side_timer'     => 0, // $request->client_side_timer
                ]);
                }
            }
        }

        if ($result) {
            return response('result_stored', 201);
        }
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
    public function update(Request $request, $id)
    {
        //
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
