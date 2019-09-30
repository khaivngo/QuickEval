<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PairedResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'picture_order_id_selected',
        'picture_order_id_left',
        'picture_order_id_right',
        'chose_none'
    ];
}
