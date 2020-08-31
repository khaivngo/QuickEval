<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['experiment_type'];


    public function results () {
        return $this->hasMany(ExperimentResult::class);
    }

    public function experiment_type () {
      return $this->belongsTo(ExperimentType::class);
    }

    public function user () {
      return $this->belongsTo(User::class);
    }
}
