<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultCategory extends Model
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
        'experiment_result_id' => 'integer',
        'picture_id_left' => 'integer',
        'category_id' => 'integer',
        'chose_none' => 'integer',
        'client_side_timer' => 'integer',
    ];


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
