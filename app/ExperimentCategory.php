<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentCategory extends Model
{
    protected $fillable = [
        'category_id',
        'experiment_id'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'experiment_id' => 'integer',
    //   'category_id' => 'integer'
    // ];

    public function category() {
        return $this->belongsTo('App\Category');
    }
}
