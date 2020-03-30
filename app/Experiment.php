<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function results () {
        return $this->hasMany(ExperimentResult::class);
    }
}
