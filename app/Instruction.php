<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    protected $fillable = [
        'user_id',
        'description'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'user_id' => 'integer'
    // ];
}
