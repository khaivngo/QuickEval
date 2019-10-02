<?php

namespace App\Exports;

use App\PairedResult;
use Maatwebsite\Excel\Concerns\FromCollection;

class PairedResultsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PairedResult::all();
        // return \App\ExperimentResult::find($id)->paired_results;
    }
}
