<?php

namespace App\Exports;

use App\ResultCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ResultCategoriesExport implements FromCollection, WithHeadings, ShouldAutoSize
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
            'session',
            'image',
            'category',
            'time spent (in seconds)',
        ];
    }
}
