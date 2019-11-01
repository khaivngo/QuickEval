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

    public function experiment_result () {
        return $this->belongsTo('App\ExperimentResult');
    }

    public function category () {
      return $this->belongsTo('App\Category', 'category_id');
    }

    public function picture () {
      return $this->belongsTo('App\Picture', 'picture_id_left');
    }
}
