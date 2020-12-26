<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentQueue extends Model
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
        'experiment_id' => 'integer',
    ];

    public function experiment_sequences () {
        return $this->hasMany(ExperimentSequence::class);
    }

    public function experiment () {
      return $this->belongsTo(Experiment::class);
    }
}
