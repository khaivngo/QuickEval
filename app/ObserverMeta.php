<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObserverMeta extends Model
{
    protected $fillable = [
        'user_id',
        'meta'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'user_id' => 'integer'
    // ];
}
