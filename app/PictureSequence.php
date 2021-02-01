<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PictureSequence extends Model
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
        'picture_order' => 'integer',
        'picture_id' => 'integer',
        'picture_queue_id' => 'integer',
    ];

    public function picture_queue () {
        return $this->belongsTo(ExperimentSequence::class);
    }
}
