<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PairedResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

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
