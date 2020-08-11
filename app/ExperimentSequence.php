<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentSequence extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function picture_set() {
        return $this->hasOne(PictureSet::class, 'id', 'picture_set_id');
    }
}
