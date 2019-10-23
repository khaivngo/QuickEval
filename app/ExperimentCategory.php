<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentCategory extends Model
{
    protected $fillable = [
        'category_id',
        'experiment_id'
    ];

    public function category() {
        return $this->belongsTo('App\Category');
    }
}
