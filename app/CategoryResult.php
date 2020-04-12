<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function category () {
      return $this->belongsTo(Category::class, 'category_id');
    }

    public function picture () {
      return $this->belongsTo(Picture::class, 'picture_id_left');
    }
}
