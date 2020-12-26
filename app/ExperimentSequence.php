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

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'picture_set_id' => 'integer',
        'experiment_queue_id' => 'integer',
        'picture_queue_id' => 'integer',
        'instruction_id' => 'integer',
    ];

    public function picture_set () {
        // return $this->hasOne(PictureSet::class, 'id', 'picture_set_id');
        return $this->belongsTo(PictureSet::class);
    }

    // public function picture_sequences () {
    //     return $this->hasManyThrough(PictureSequence::class, PictureQueue::class);
    // }

    public function picture_queue () {
        return $this->belongsTo(PictureQueue::class);
    }
}
