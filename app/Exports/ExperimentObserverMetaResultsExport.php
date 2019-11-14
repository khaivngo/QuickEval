<?php

namespace App\Exports;

use App\ExperimentObserverMetaResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class ExperimentObserverMetaResultsExport implements FromCollection, WithHeadings
{
    use Exportable;

    public $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function collection() {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'observer',
            'meta',
            'answer'
        ];
    }
}
