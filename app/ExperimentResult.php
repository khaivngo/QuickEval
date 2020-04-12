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

    public function paired_results () {
        return $this->hasMany(PairedResult::class);
    }

    public function rank_order_results () {
        return $this->hasMany(RankOrderResult::class);
    }

    public function triplet_results () {
        return $this->hasMany(TripletResult::class);
    }

    public function category_results () {
        return $this->hasMany(CategoryResult::class);
    }
}
