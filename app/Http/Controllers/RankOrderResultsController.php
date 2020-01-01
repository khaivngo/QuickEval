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
            ::with('rank_order_results.ranking', 'rank_order_results.picture', 'rank_order_results.picture_set')
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
            ::with('rank_order_results.ranking', 'rank_order_results.picture', 'rank_order_results.picture_set')
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
