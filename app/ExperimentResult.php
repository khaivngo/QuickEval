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

    // public function results ()
    // {
    //     return $this->hasMany('App\Result', 'user_id', 'experiment_id'); # GJØR OM DISSE TIL foreign KEY
    // }

    public function paired_results ()
    {
        return $this->hasMany('App\PairedResult');
    }
}
