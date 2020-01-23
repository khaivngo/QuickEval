<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RankOrderResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'picture_set_id',
        'picture_id',
        'ranking'
    ];

    // protected $casts = [
    //   'id'                    => 'integer',
    //   'experiment_result_id'  => 'integer',
    //   'picture_set_id'        => 'integer',
    //   'picture_id'            => 'integer',
    //   'ranking'               => 'integer'
    // ];

    public function experiment_result () {
        return $this->belongsTo('App\ExperimentResult');
    }

    public function picture () {
        return $this->belongsTo('App\Picture', 'picture_id');
    }

    public function picture_set () {
        return $this->belongsTo('App\PictureSet', 'picture_set_id');
    }
}
