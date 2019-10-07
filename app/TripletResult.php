<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TripletResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'category_id_left',
        'category_id_middle',
        'category_id_right',
        'picture_id_left',
        'picture_id_middle',
        'picture_id_right',
        'chose_none'
    ];
}
