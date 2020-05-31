<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultRankOrder extends Model
{
    protected $guarded = [];

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
