<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ExperimentResult;
use App\RankOrderResult;
use App\Exports\RankOrderResultsExport;
use Maatwebsite\Excel\Facades\Excel;

class RankOrderResultsController extends Controller
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
      }

      RankOrderResult::insert($rankings);

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


    public function export_observer(int $id)
    {
        $experiment_results = ExperimentResult::find($id);
        $rank_order_results = ExperimentResult
            ::with('rank_order_results.picture', 'rank_order_results.picture_set')
            ->find($id)
            ->rank_order_results;

        $data = [];
        foreach ($rank_order_results as $result)
        {
            $arr = [];
            $arr['observer'] = $experiment_results->user_id;
            $arr['session']  = $experiment_results->id;
            $arr['ranking']  = $result->ranking;
            $arr['picture']  = $result->picture->name;
            $arr['set']      = $result->picture_set->title;
            array_push($data, $arr);
        }

        $file_ext = 'csv';
        $filename = 'results-' . $experiment_results->user_id . '.csv';

        return Excel::download(new RankOrderResultsExport($data), $filename);
    }


    public function export_all(int $id)
    {
        // TODO: check if scientist owns experiment and that results belong to experiment

        $rank_order_results = ExperimentResult
            ::with('rank_order_results.picture', 'rank_order_results.picture_set')
            ->where('experiment_id', $id)
            ->get();

        $data = [];
        foreach ($rank_order_results as $result) {
          foreach ($result->rank_order_results as $res) {
            $arr = [];
            $arr['observer'] = $result->user_id;
            $arr['session']  = $result->id;
            $arr['ranking']  = $res->ranking;
            $arr['picture']  = $res->picture->name;
            $arr['set']      = $res->picture_set->title;

            array_push($data, $arr);
          }
        }

        $file_ext = 'csv';
        $filename = 'results.' . $file_ext;

        return Excel::download(new RankOrderResultsExport($data), $filename);
    }

    public function statistics(int $id) {
        $results = [];

        # get the image sets used in a experiment
        $sets = \DB::table('experiment_queues')
          ->join('experiment_sequences', 'experiment_sequences.experiment_queue_id', '=', 'experiment_queues.id')
          ->leftJoin('picture_sets', 'experiment_sequences.picture_set_id', '=', 'picture_sets.id')
          ->where([
            ['experiment_queues.experiment_id', '=', $id],
            ['experiment_sequences.picture_queue_id', '!=', null]
          ])
          ->get();
        $results['imageSets'] = $sets;


        # each image set with belonging images
        $d = [];
        foreach ($sets as $set) {
          $dd = \App\Picture::where([
            ['picture_set_id', '=', $set->picture_set_id],
            ['is_original', '=', 0]
          ])->get();

          array_push($d, $dd);
        }
        $results['imagesForEachImageSet'] = $d;


        # original images
        foreach ($sets as $key => $set) {
          $dd = \App\Picture::where([
            ['picture_set_id', '=', $set->picture_set_id],
            ['is_original', '=', 1]
          ])->first();

          $results['imageUrl'][$key] = $dd;
        }


        function build_sorter($key) {
            return function ($a, $b) use ($key) {
                return strnatcmp($a[$key], $b[$key]);
            };
        }

        $rank_order_results = ExperimentResult
          ::with('rank_order_results.picture', 'rank_order_results.picture_set')
          ->where('experiment_id', $id)
          // ->groupBy('rank_order_results.experiment_result_id')
          ->get();

        $data = [];
        foreach ($rank_order_results as $result)
        {
          $one = [];
          foreach ($result->rank_order_results as $key => $res)
          {
            $arr = [];

            $arr['ranking']   = $res->ranking;
            $arr['pictureId'] = $res->picture->id;
            $arr['name']      = $res->picture->name;
            $arr['user']      = $result->user_id;
            $arr['setId']     = $res->picture_set->id;
            $arr['setName']   = $res->picture_set->title;

            array_push($one, $arr);
          }
          // sort by pictureId ascending
          usort($one, build_sorter('pictureId')); // do this in sql?
          array_push($data, $one);
        }
        // function personSort( $a, $b ) {
        //     return $a->age == $b->age ? 0 : ( $a->age > $b->age ) ? 1 : -1;
        // }
        // usort( $data, 'personSort' );
        // $results['resultsForEachImageSet'] = $data;

        // $imageSetIds = array_map(function($o) { return $o['id']; }, (array) $sets);
        $imageSetIds = [];
        foreach ($sets as $set) {
          $imageSetIds[] = $set->id;
        }

        # group results by image set
        $new = [];
        foreach ($data as $key => $resultArray)
        {
          foreach ($resultArray as $keyy => $result)
          {
            $index = array_search($result['setId'], $imageSetIds);
            $new[$index][] = $result;
          }
        }

        # group results by image set
        // $new = [];
        // foreach ($data as $result) {
        //   foreach ($results['imagesForEachImageSet'] as $key => $images) {
        //     foreach ($images as $image) {
        //       if ($result[0]['setId'] == $image['picture_set_id']) {
        //         $new[$key][] = $result;
        //       }
        //     }
        //   }
        // }
        $results['resultsForEachImageSet'] = $new;


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
