<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PictureQueue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function picture_sequence () {
        return $this->hasMany(PictureSequence::class);
    }
}
