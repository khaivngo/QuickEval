<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultMatchEstimation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'experiment_result_id' => 'integer',
        'magnitude_value' => 'integer',
        'picture_id_left' => 'integer',
        'picture_id_right' => 'integer',
        'chose_none' => 'integer',
        'client_side_timer' => 'integer',
    ];

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function picture () {
        return $this->belongsTo(Picture::class, 'picture_id_left');
    }
}
