<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultRankOrder extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'experiment_result_id' => 'integer',
        'picture_set_id' => 'integer',
        'picture_id' => 'integer',
        'ranking' => 'integer',
        'client_side_timer' => 'integer',
    ];

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function picture () {
        return $this->belongsTo(Picture::class, 'picture_id');
    }

    public function picture_set () {
        return $this->belongsTo(PictureSet::class, 'picture_set_id');
    }
}
