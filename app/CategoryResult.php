<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'picture_id_left',
        'category_id',
        'chose_none'
    ];
}
