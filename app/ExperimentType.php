<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentType extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    // protected $casts = [
    //   'id' => 'integer'
    // ];
}
