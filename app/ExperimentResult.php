<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentResult extends Model
{
    protected $fillable = [
        'experiment_id',
        'user_id',
        'browser',
        'os',
        'x',
        'y',
        'start_time',
        'end_time',
        'completed'
    ];
}
