<?php

namespace App\Exports;

use App\TripletResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class TripletResultsExport implements FromCollection, WithHeadings
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
            'left image',
            'middle image',
            'right image',
            'left category ',
            'middle category',
            'right category',
        ];
    }
}
