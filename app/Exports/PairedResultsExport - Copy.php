<?php

namespace App\Exports;

use App\PairedResult;
use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

class PairedResultsExport implements FromCollection, WithHeadings
{
    use Exportable;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    // public function query()
    // {
    //   // return PairedResult::select('id', 'picture_id_selected', 'created_at');
    //   // return Invoice::query()->whereYear('created_at', $this->year);

    //   return $this->data;
    // }

    public function collection()
    {
        return collect($this->data);
    }

    /**
     * @var PairedResult $pairedresult
     */
    // public function map($paired_result): array
    // {
    //     return [
    //         $paired_result->selected,
    //         $paired_result->left,
    //         $paired_result->right,
    //         // Date::dateTimeToExcel($paired_result->created_at),
    //     ];
    // }

    public function headings(): array
    {
        return [
            'left image',
            'right image',
            'selected image',
        ];
    }
}
