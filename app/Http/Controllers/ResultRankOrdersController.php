<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ResultRankOrder;
use App\Exports\ResultRankOrdersExport;
use Maatwebsite\Excel\Facades\Excel;

use App\ExperimentResult;
use App\ExperimentQueue;
use App\Experiment;
use App\ResultObserverMeta;
use App\ExperimentObserverMeta;

class ResultRankOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $rankings = [];
      foreach ($request->rankings as $key => $image) {
        $rankings[$key]['experiment_result_id'] = $request->experiment_result_id;
        $rankings[$key]['picture_set_id']       = $image['picture_set_id'];
        $rankings[$key]['picture_id']           = $image['picture_id'];
        $rankings[$key]['ranking']              = $key + 1;
        $rankings[$key]['client_side_timer']    = $request->client_side_timer;
      }

      ResultRankOrder::insert($rankings);

      return response('result_stored', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        return ExperimentResult::find($id)->rank_order_results;
    }

    /**
     * Export a listing of the resource in a CSV/Excel/HTML file.
     *
     * @return \Maatwebsite\Excel\Facades\Excel
     */
    public function export (Request $request) {
      // TODO: check if scientist owns experiment and that results belong to experiment
      $results = [];
      $expID = $request->experimentId;

      # get all rank order results for each observer
      $observers = ExperimentResult
        ::with('rank_order_results.picture', 'rank_order_results.picture_set')
        ->whereIn('id', $request->selected)
        ->get();

      $results['observers'] = $observers;

      # get all rank order results for each selected observer
      if ($request->flags['results']) {
        # construct and array with result data for exporting
        $data = [];
        foreach ($observers as $observer) {
          foreach ($observer->rank_order_results as $key => $result) {
            $arr = [];
            $arr['observer'] = $observer->user_id;
            $arr['session']  = $observer->id;
            $arr['ranking']  = $result->ranking;
            $arr['picture']  = $result->picture->name;
            $arr['set']      = $result->picture_set->title;
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
      return Excel::download(new ResultRankOrdersExport($results), $filename);
    }


    public function statistics (Request $request, int $id)
    {
        $results = [];

        # Get the image sets used in the experiment (every image set used in a experiment sequences).
        $data =
          ExperimentQueue::with(['experiment_sequences' => function ($query) {
            $query->where('experiment_sequences.picture_queue_id', '!=', NULL)
              ->with(['picture_set.pictures' => function ($q) {
                $q->where('is_original', 0);
              }]);
          }])
          ->where('experiment_id', $id)
          ->get();

        // TODO: remove need for this on frontend
        $results['imagesForEachImageSet'] = $data[0]->experiment_sequences;
        $results['imageSets'] = $data[0]->experiment_sequences;


        // # sorts ascending based on provided id
        // function build_sorter($key) {
        //     return function ($a, $b) use ($key) {
        //         return strnatcmp($a[$key], $b[$key]);
        //     };
        // }

        $matchThese = ['experiment_id' => $id];
        // exclude incomplete data?
        if ($request->includeIncomplete == false) {
          $matchThese['completed'] = 1;
        }

        $rank_order_results = ExperimentResult::with([
            'rank_order_results' => function ($query) { $query->orderBy('picture_id'); },
            'rank_order_results.picture'
          ])->where($matchThese)
          ->get();

        $data = [];
        foreach ($rank_order_results as $result) {
          $one = [];
          $one = $result->rank_order_results->groupBy('picture_set_id');
          array_push($data, $one);
        }

        $results['resultsForEachObserver'] = $data;

        // $index = array_search($result['setId'], $imageSetIds);
        // $new[$index][] = $result;

        # group results by image sets (the array $key is the same as image set id)

        // TODO: replace with groupBy function... so that we don't need collect() below
        $groupedByImageSet = [];
        foreach ($data as $observer) {
          foreach ($observer as $key => $set) {
            $groupedByImageSet[$key][] = $set;
          }
        }

        # group each observers (one session) answers
        $data = [];
        foreach ($groupedByImageSet as $key => $set) {
          $one = [];
          $collection = collect($set);
          $one = $collection->groupBy(function ($item, $key) {
              return $item[0]['experiment_result_id'];
          });
          array_push($data, $one);
        }
        $results['resultsForEachImageSet'] = $data;

        return response($results);
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
