<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExperimentSlider extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'experiment_id' => 'integer',
        'min_value' => 'integer',
        'max_value' => 'integer'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
