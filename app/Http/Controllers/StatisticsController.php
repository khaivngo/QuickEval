<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Experiment;
use App\ExperimentResult;
use App\ResultPair;
use App\ResultCategory;
use App\ResultMagnitudeEstimation;
use App\ResultTriplet;
use App\ResultRankOrder;
use App\Picture;

class StatisticsController extends Controller
{
    public function index () {
        $users_count = User::where('role', '>', 1)->count();

        $experiments_count = Experiment::all()->count();
        $experiment_results_count = ExperimentResult::all()->count(); // where completed?

        $result_pair_count      = ResultPair::all()->count();
        $result_category_count  = ResultCategory::all()->count();
        $result_magnitude_count = ResultMagnitudeEstimation::all()->count();
        $result_triplet_count   = ResultTriplet::all()->count();
        $result_rank_count      = ResultRankOrder::all()->count();
        $result_total_count = $result_pair_count + $result_category_count + $result_magnitude_count + $result_triplet_count + $result_rank_count;

        $pictures_count = Picture::all()->count();

        $counts = [
            'users' => $users_count,
            'experiments' => $experiments_count,
            'experimentResults' => $experiment_results_count,
            'resultsPaired' => $result_pair_count,
            'resultsCategory' => $result_category_count,
            'resultsMagnitude' => $result_magnitude_count,
            'resultsTriplet' => $result_triplet_count,
            'resultRankOrder' => $result_rank_count,
            'resultsTotal' => $result_total_count,
            'pictures' => $pictures_count,
        ];

        return response($counts);
    }
}
