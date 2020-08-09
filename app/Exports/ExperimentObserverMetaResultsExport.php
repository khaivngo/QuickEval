<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExperimentObserverMetaResultsExport implements FromView
{
    use Exportable;

    public function view(): View
    {
        return view('exports.meta', [
            // 'invoices' => Invoice::all()
        ]);
    }

    // public $data;

    // public function __construct($data) {
    //     $this->data = $data;
    // }

    // public function collection() {
    //     return collect($this->data);
    // }

    // public function headings(): array
    // {
    //     return [
    //         'observer',
    //         'meta',
    //         'answer'
    //     ];
    // }
}
