<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_original' => 'integer',
        'picture_set_id' => 'integer',
    ];

    public function picture_set () {
      return $this->belongsTo(PictureSet::class);
    }

    // public function picture_sequence () {
    //   return $this->belongsTo(PictureSequence::class);
    // }
}
