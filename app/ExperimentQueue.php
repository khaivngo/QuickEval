<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentQueue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function experiment_sequences() {
        return $this->hasMany(ExperimentSequence::class);
    }
}
