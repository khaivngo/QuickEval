<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PictureSequence extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    // public function experiment_sequence () {
    //     return $this->belongsTo(ExperimentSequence::class);
    // }
}