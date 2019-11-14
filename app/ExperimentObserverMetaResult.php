<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentObserverMetaResult extends Model
{
    protected $fillable = [
        'experiment_id',
        'user_id',
        'observer_meta_id',
        'answer'
    ];

    // public function observer_meta () {
    //   return $this->belongsTo(ObserverMeta::class);
    // }

    // public function user () {
    //   return $this->belongsTo(User::class);
    // }
}
