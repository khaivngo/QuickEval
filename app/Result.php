<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'experiment_id',
        'experiment_type_id',
        'picture_order_id',
        'category_id',
        'choose_noone'
    ];
}
