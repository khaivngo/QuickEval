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
    protected $with = ['type'];


    public function results () {
        return $this->hasMany(ExperimentResult::class);
    }

    public function observer_metas () {
        return $this->hasMany(ExperimentObserverMeta::class);
    }

    // public function experiment_queues () {
    //   return $this->hasMany(ExperimentQueue::class);
    // }

    public function sequences () {
        return $this->hasManyThrough(ExperimentSequence::class, ExperimentQueue::class);
    }

    public function type () {
      return $this->belongsTo(ExperimentType::class, 'experiment_type_id');
    }

    public function user () {
      return $this->belongsTo(User::class);
    }
}
