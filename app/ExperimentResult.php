<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function paired_results () {
        return $this->hasMany(ResultPair::class);
    }

    public function rank_order_results () {
        return $this->hasMany(ResultRankOrder::class);
    }

    public function triplet_results () {
        return $this->hasMany(ResultTriplet::class);
    }

    public function category_results () {
        return $this->hasMany(ResultCategory::class);
    }
}
