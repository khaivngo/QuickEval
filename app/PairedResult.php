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

    // protected $casts = [
    //   'id'                   => 'integer',
    //   'experiment_result_id' => 'integer',
    //   'picture_id_selected'  => 'integer',
    //   'picture_id_left'      => 'integer',
    //   'picture_id_right'     => 'integer',
    //   'chose_none'           => 'integer'
    // ];

    public function experiment_result () {
        return $this->belongsTo('App\ExperimentResult');
    }

    public function picture_selected () {
        return $this->belongsTo('App\Picture', 'picture_id_selected');
    }

    public function picture_left () {
        return $this->belongsTo('App\Picture', 'picture_id_left');
    }

    public function picture_right () {
        return $this->belongsTo('App\Picture', 'picture_id_right');
    }
}
