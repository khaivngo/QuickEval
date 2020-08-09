<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use App\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ResultPairsExport implements FromView
{
    
    use Exportable;

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.results', [
            'results' => $this->data
        ]);
    }


    // public function collection() {
    //     return collect($this->data);
    // }

    // public function headings(): array
    // {
    //     return [
    //         'observer',
    //         'session',
    //         'left image',
    //         'right image',
    //         'selected image',
    //         'time spent (in seconds)'
    //     ];
    // }
}
