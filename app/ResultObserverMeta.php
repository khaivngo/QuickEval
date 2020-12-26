<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultObserverMeta extends Model
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
        'observer_meta_id' => 'integer',
    ];

    public function observer_meta () {
        return $this->belongsTo(ObserverMeta::class);
    }

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }
}
