<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'user_id',
        'title'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'user_id' => 'integer'
    // ];
}
