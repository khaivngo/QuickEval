<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentObserverMeta extends Model
{
    protected $fillable = [
        'experiment_id',
        'observer_meta_id'
    ];
}
