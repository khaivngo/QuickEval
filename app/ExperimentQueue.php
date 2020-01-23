<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentQueue extends Model
{
    protected $fillable = [
        'experiment_id'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'experiment_id' => 'integer'
    // ];
}
