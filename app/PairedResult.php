<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PairedResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'picture_id_selected',
        'picture_id_left',
        'picture_id_right',
        'chose_none'
    ];

    public function experiment_result () {
        return $this->belongsTo('App\ExperimentResult');
    }
}
