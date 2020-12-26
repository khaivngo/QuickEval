<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ExperimentResult extends Model
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'experiment_id' => 'integer',
        'user_id' => 'integer',
        'start_time' => 'integer',
        'end_time' => 'integer',
        'completed' => 'integer',
        // 'x' => 'integer',
        // 'y' => 'integer',
    ];

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
