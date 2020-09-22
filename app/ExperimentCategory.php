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

    public function category () {
        return $this->belongsTo(Category::class);
    }
}
