<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ResultPairsExport implements FromView, ShouldAutoSize
{
    use Exportable;

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.paired', [
            'results' => $this->data
        ]);
    }
}
