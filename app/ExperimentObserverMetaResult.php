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
}
