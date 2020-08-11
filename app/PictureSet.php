<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PictureSet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function pictures () {
        return $this->hasMany(Picture::class);
    }

    public function experiment_sequences () {
        return $this->hasMany(ExperimentSequence::class);
    }
}
