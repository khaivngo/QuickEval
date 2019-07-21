<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PictureQueue extends Model
{
    protected $fillable = [
        'picture_order',
        'picture_id',
        'picture_queue_id'
    ];
}
