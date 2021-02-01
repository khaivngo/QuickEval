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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'experiment_result_id' => 'integer',
        'picture_id_selected' => 'integer',
        'picture_id_left' => 'integer',
        'picture_id_right' => 'integer',
        'chose_none' => 'integer',
        'client_side_timer' => 'integer',
    ];

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
