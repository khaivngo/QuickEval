<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultPair extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // protected $table = 'my_flights';

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function picture_selected () {
        return $this->belongsTo(Picture::class, 'picture_id_selected');
    }

    public function picture_left () {
        return $this->belongsTo(Picture::class, 'picture_id_left');
    }

    public function picture_right () {
        return $this->belongsTo(Picture::class, 'picture_id_right');
    }
}
