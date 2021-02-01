<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'integer',
        'experiment_id' => 'integer',
    ];

    public function category () {
        return $this->belongsTo(Category::class);
    }
}
