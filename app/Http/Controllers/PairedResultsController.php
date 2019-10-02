<?php

namespace App\Http\Controllers;

use App\Exports\PairedResultsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class PairedResultsController extends Controller
{
  public function index ($id) {
    // return \App\PairedResult::where('experiment_result_id', $id)->get();

    // return \App\ExperimentResult::find($id)->paired_results;
  }

  /**
   * The \Maatwebsite\Excel package is used for creating exports.
   * See: https://docs.laravel-excel.com/3.1/exports/
   * Also see files in the app/Exports folder
   */
  public function export ($id)
  {
      // TODO $id
      // ->join(image_names)

      return Excel::download(new PairedResultsExport, 'results.xlsx'); // TODO: append user_id or created_at to filename
      // return response('result_stored', 201);
      // return (new PairedResultsExport)->download('results.csv', \Maatwebsite\Excel\Excel::CSV);
  }

  public function index_export ($id) {
    // TODO: check if scientist owns experiment and that results belong to experiment

    return \App\PairedResult
      ::where('experiment_result_id', $id)
      ->get();
  }

  public function all () {
    return \App\PairedResult::where('experiment_result_id', $id)->get();

    // get alle som tilhører en experiment results som har experiment_id = value
    // definerer reletationship i model... mange til mange forhold?
  }

  public function store (Request $request) {
    $result = \App\PairedResult::create([
      'experiment_result_id'  => $request->experiment_result_id,
      'picture_id_selected'   => $request->picture_id_selected,
      'picture_id_left'       => $request->picture_id_left,
      'picture_id_right'      => $request->picture_id_right,
      'chose_none'            => $request->chose_none
    ]);

    if ($result) {
      return response('result_stored', 201);
    }
  }
}
