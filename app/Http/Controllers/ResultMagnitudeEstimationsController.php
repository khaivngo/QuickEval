<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exports\ResultMagnitudesExport;
use Maatwebsite\Excel\Facades\Excel;

use App\ResultMagnitudeEstimation;
use App\ExperimentResult;
use App\ExperimentQueue;
use App\Experiment;
use App\ResultObserverMeta;
use App\ExperimentObserverMeta;

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
     * Export a listing of the resource in a CSV file.
     *
     * @return \Maatwebsite\Excel\Facades\Excel
     */
    public function export (Request $request) {
        // TODO: check if scientist owns experiment and that results belong to experiment
        $results = [];
        $expID = $request->experimentId;
  
        $observers = ExperimentResult
        //   ::with('category_results.picture', 'category_results.category')
          ::with('magnitude_results.picture')
          ->whereIn('id', $request->selected)
          ->get();
  
        $results['observers'] = $observers;
  
        # get all results for each selected observer
        if ($request->flags['results'])
        {
          # construct and array with result data for exporting
          $data = [];
          foreach ($observers as $observer) {
            foreach ($observer->magnitude_results as $key => $result) {
              $arr = [];
              $arr['observer']   = $observer->user_id;
              $arr['session']    = $observer->id;
              $arr['picture']    = $result->picture->name;
              // $arr['category']   = $result->category->title;
              $arr['magnitude']  = $result->magnitude;
              $arr['time_spent'] = $result->client_side_timer;
  
              array_push($data, $arr);
            }
          }
  
          $results['results'] = $data;
        }
  
        # get all the image sets, with images, used in the experiment
        if ($request->flags['imageSets']) {
          $data =
            ExperimentQueue::with(['experiment_sequences' => function ($query) {
                $query->where('experiment_sequences.picture_queue_id', '!=', NULL)
                  ->with('picture_set.pictures');
            }])
            ->where('experiment_id', '=', $expID)
            ->get();
  
          $results['imageSets'] = $data[0]->experiment_sequences;
        }
  
        # get observer input answers for selected observers
        if ($request->flags['inputs']) {
          $result_observer_metas =
            ResultObserverMeta::with('observer_meta', 'experiment_result.user')
              ->whereIn('experiment_result_id', $request->selected)
              ->get();
  
          $results['inputs'] = $result_observer_metas;
        }
  
        # get all meta data for observer input fields used in the experiment
        if ($request->flags['inputsMeta']) {
            $results['inputsMeta'] = ExperimentObserverMeta::with('observer_meta')
                ->where('experiment_id', $expID)
                ->get();
        }
  
        # get meta data about experiment
        if ($request->flags['expMeta']) {
            $expMeta = Experiment::find($expID);

            # create array in a export ready format
            $data = [];
            $data['title']            = ['title', $expMeta->title];
            $data['experiment_type']  = ['experiment type', $expMeta->type->name];
            $data['delay']            = ['delay between stimuli switching', $expMeta->delay . 'ms'];
            $data['background_colour']= ['Background colour', '#' . $expMeta->background_colour];
            $data['stimuli_spacing']  = ['Stimuli spacing', $expMeta->stimuli_spacing . 'px'];
            $data['same_pair']        = ['Same pair twice (flipped)', ($expMeta->same_pair == 1) ? 'yes' : 'no'];
            $data['show_original']    = ['Show original', ($expMeta->show_original == 1) ? 'yes' : 'no'];

            $results['expMeta'] = $data;
        }
  
        # use the user selected file format if it exists in the whitelist array, else default to CSV
        $file_ext = in_array($request->fileFormat, ['csv','xlsx', 'html']) ? $request->fileFormat : 'csv';
        $filename = 'results.' . $file_ext;
  
        # see: https://docs.laravel-excel.com/3.1/exports/
        return Excel::download(new ResultMagnitudesExport($results), $filename);
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
