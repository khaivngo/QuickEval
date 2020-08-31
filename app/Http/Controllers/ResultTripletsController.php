<?php

namespace App\Http\Controllers;

use App\Exports\ResultTripletsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

use App\ResultTriplet;
use App\ExperimentResult;
use App\ExperimentQueue;
use App\Experiment;
use App\ResultObserverMeta;
use App\ExperimentObserverMeta;

class ResultTripletsController extends Controller
{
    /**
     * Export a listing of the resource in a CSV file.
     *
     * @return \Maatwebsite\Excel\Facades\Excel
     */
    public function export (Request $request) {
      // TODO: check if scientist owns experiment and that results belong to experiment
      $results = [];
      $expID = $request->experimentId;

      if ($request->flags['results']) {
        # get all triplet results for each observer
        $observers = ExperimentResult
          ::with(
            'triplet_results.picture_left', 'triplet_results.picture_middle', 'triplet_results.picture_right',
            'triplet_results.category_left', 'triplet_results.category_middle', 'triplet_results.category_right'
          )
          ->whereIn('id', $request->selected)
          ->get();

        # construct and array with result data for exporting
        $data = [];
        foreach ($observers as $observer) {
          foreach ($observer->triplet_results as $key => $result) {
            $arr = [];
            $arr['observer']       = $observer->user_id;
            $arr['session']        = $observer->id;
            $arr['picture_left']   = $result->picture_left->name;
            $arr['picture_middle'] = $result->picture_middle->name;
            $arr['picture_right']  = $result->picture_right->name;

            $arr['category_left']   = $result->category_left->title;
            $arr['category_middle'] = $result->category_middle->title;
            $arr['category_right']  = $result->category_right->title;

            $arr['time_spent']      = $result->client_side_timer;

            array_push($data, $arr);
          }
        }

        $results['results'] = $data;
      }

      # get all the image sets, with images, used in the experiments experiment_sequences
      if ($request->flags['imageSets']) {
        $data =
          ExperimentQueue::with(['experiment_sequences' => function ($query) {
              $query->where('experiment_sequences.picture_queue_id', '!=', NULL)->with('picture_set.pictures');
          }])
          ->where('experiment_id', '=', $expID)
          ->get();

        $results['imageSets'] = $data[0]->experiment_sequences;
      }

      # get observer input answers for selected observers
      if ($request->flags['inputs']) {
        $experiment_observer_meta_results =
          ResultObserverMeta::with('observer_meta', 'experiment_result.user')
            ->whereIn('experiment_result_id', $request->selected)
            ->get();

        $results['inputs'] = $experiment_observer_meta_results;
      }

      # get all meta data for observer input fields used in the experiment
      if ($request->flags['inputsMeta']) {
        $results['inputsMeta'] = ExperimentObserverMeta::with('observer_meta')->where('experiment_id', $expID)->get();
      }

      # get meta data about experiment
      if ($request->flags['expMeta']) {
        $expMeta = Experiment::find($expID);

        # create array in a export ready format
        $data = [];
        $data['title']            = ['title', $expMeta->title];
        $data['experiment_type']  = ['experiment type', $expMeta->experiment_type->name];
        $data['delay']            = ['delay between stimuli switching', $expMeta->delay];
        $data['background_colour']= ['Background colour', $expMeta->background_colour];
        $data['stimuli_spacing']  = ['Stimuli spacing', $expMeta->stimuli_spacing . 'px'];
        $data['same_pair']        = ['Same pair twice (flipped)', ($expMeta->same_pair == 1) ? 'yes' : 'no'];
        $data['show_original']    = ['Show original', ($expMeta->show_original == 1) ? 'yes' : 'no'];

        $results['expMeta'] = $data;
      }

      # use the user selected file format if it exists in whitelist array, else default to CSV
      $file_ext = in_array($request->fileFormat, ['csv','xlsx', 'html']) ? $request->fileFormat : 'csv';
      $filename = 'results.' . $file_ext;

      # see: https://docs.laravel-excel.com/3.1/exports/
      return Excel::download(new ResultTripletsExport($results), $filename);
    }


    public function all () {
      // get alle som tilhører en experiment results som har experiment_id = value
      // definerer reletationship i model... mange til mange forhold?
      return ResultTriplet::where('experiment_result_id', $id)->get();
    }

    public function store (Request $request) {
      $result = ResultTriplet::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id_left'      => $request->category_id_left,
        'category_id_middle'    => $request->category_id_middle,
        'category_id_right'     => $request->category_id_right,
        'picture_id_left'       => $request->picture_id_left,
        'picture_id_middle'     => $request->picture_id_middle,
        'picture_id_right'      => $request->picture_id_right,
        'chose_none'            => $request->chose_none,
        'client_side_timer'     => $request->client_side_timer
      ]);

      return response('result_stored', 201);
    }
}
