<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exports\ResultCategoriesExport;
use Maatwebsite\Excel\Facades\Excel;

use App\ResultCategory;
use App\ExperimentResult;
use App\ExperimentQueue;
use App\Experiment;
use App\ResultObserverMeta;
use App\ExperimentObserverMeta;

class ResultCategoriesController extends Controller
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

      $observers = ExperimentResult
        ::with('category_results.picture', 'category_results.category')
        ->whereIn('id', $request->selected)
        ->get();

      $results['observers'] = $observers;

      # get all results for each selected observer
      if ($request->flags['results'])
      {
        # construct and array with result data for exporting
        $data = [];
        foreach ($observers as $observer) {
          foreach ($observer->category_results as $key => $result) {
            $arr = [];
            $arr['observer']   = $observer->user_id;
            $arr['session']    = $observer->id;
            $arr['picture']    = $result->picture->name;
            $arr['category']   = $result->category->title;
            $arr['time_spent'] = $result->client_side_timer;

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
        $result_observer_metas =
          ResultObserverMeta::with('observer_meta', 'experiment_result.user')
            ->whereIn('experiment_result_id', $request->selected)
            ->get();

        $results['inputs'] = $result_observer_metas;
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
        $data['experiment_type']  = ['experiment type', $expMeta->type->name];
        $data['delay']            = ['delay between stimuli switching', $expMeta->delay . 'ms'];
        $data['background_colour']= ['Background colour', '#' . $expMeta->background_colour];
        $data['stimuli_spacing']  = ['Stimuli spacing', $expMeta->stimuli_spacing . 'px'];
        $data['same_pair']        = ['Same pair twice (flipped)', ($expMeta->same_pair == 1) ? 'yes' : 'no'];
        $data['show_original']    = ['Show original', ($expMeta->show_original == 1) ? 'yes' : 'no'];

        $results['expMeta'] = $data;
      }

      # use the user selected file format if it exists in whitelist array, else default to CSV
      $file_ext = in_array($request->fileFormat, ['csv','xlsx', 'html']) ? $request->fileFormat : 'csv';
      $filename = 'results.' . $file_ext;

      # see: https://docs.laravel-excel.com/3.1/exports/
      return Excel::download(new ResultCategoriesExport($results), $filename);
    }


    public function store (Request $request) {
      $result = ResultCategory::create([
        'experiment_result_id'  => $request->experiment_result_id,
        'category_id'           => $request->category_id,
        'picture_id_left'       => $request->picture_id_left,
        'chose_none'            => $request->chose_none,
        'client_side_timer'     => $request->client_side_timer
      ]);

      if ($result) {
        return response('result_stored', 201);
      }
    }

    public function statistics (Request $request, int $id) {
      $results = [];

      # Get the image sets used in the experiment (every image set used in experiment sequences).
      $seq =
        ExperimentQueue::with(['experiment_sequences' => function ($query) {
          $query->where('experiment_sequences.picture_queue_id', '!=', NULL)
            ->with(['picture_set.pictures' => function ($q) {
              $q->where('is_original', 0);
            }]);
        }])
        ->where('experiment_id', $id)
        ->get();

      $results['categories'] = \App\ExperimentCategory::with('category')->where('experiment_id', $id)->get();

      $matchThese = ['experiment_id' => $id];
      // exclude incomplete data?
      if ($request->includeIncomplete == false) {
        $matchThese['completed'] = 1;
      }

      # get all results for each observer
      $observers = ExperimentResult
        ::with('category_results.picture.picture_set', 'category_results.category')
        ->where($matchThese)
        ->get();

      $data = [];
      // $hello = $observers->groupBy('picture_id_left');
      foreach ($observers as $observer) {
        foreach ($observer->category_results as $key => $result) {
          $arr = [];
          $arr['observer']      = $observer->user_id;
          $arr['session']       = $observer->id;
          $arr['category_id']   = $result->category->id;
          $arr['category']      = $result->category->title;
          $arr['picture_id']    = $result->picture_id_left;
          $arr['picture']       = $result->picture->name;
          $arr['picture_set_id'] = $result->picture->picture_set->id;
          $arr['time_spent']    = $result->client_side_timer;

          array_push($data, $arr);
        }
      }

      $results['results'] = $data;

      # Add an array of categories used in the experiment to each image used in the experiment. Then add a empty result array to
      # every category which we use later for storing results that belongs to that image/category combination.
      foreach ($seq[0]->experiment_sequences as $sequence) {
        foreach ($sequence['picture_set']['pictures'] as $image) {
          $image['categories'] = $results['categories'];
          foreach ($image['categories'] as $category) {
            $category['result'] = [];
          }
        }
      }
      $results['imagesetSequences'] = $seq[0]->experiment_sequences;

      return $results;
    }
}
