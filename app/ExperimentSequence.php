<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentSequence extends Model
{
    protected $fillable = [
        'picture_set_id',
        'experiment_queue_id',
        'picture_queue_id',
        'instruction_id'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'picture_set_id' => 'integer',
    //   'experiment_queue_id' => 'integer',
    //   'picture_queue_id' => 'integer',
    //   'instruction_id' => 'integer'
    // ];
}
