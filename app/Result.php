<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'user_id',
        'experiment_id',
        // 'experiment_result_id',
        'picture_order_id',
        'category_id',
        'chose_none'
    ];
}
