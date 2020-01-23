<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentResult extends Model
{
    protected $fillable = [
        'experiment_id',
        'user_id',
        'browser',
        'os',
        'x',
        'y',
        'start_time',
        'end_time',
        'completed'
    ];

    // protected $casts = [
    //   'id' => 'integer',
    //   'experiment_id' => 'integer',
    //   'user_id' => 'integer',
    //   'x' => 'integer',
    //   'y' => 'integer',
    //   'start_time' => 'integer',
    //   'end_time' => 'integer',
    //   'completed' => 'integer'
    // ];

    // public function results ()
    // {
    //     return $this->hasMany('App\Result', 'user_id', 'experiment_id'); # GJØR OM DISSE TIL foreign KEY
    // }

    public function paired_results () {
        return $this->hasMany('App\PairedResult');
    }

    public function rank_order_results () {
        return $this->hasMany('App\RankOrderResult');
    }

    public function triplet_results () {
        return $this->hasMany('App\TripletResult');
    }

    public function category_results () {
        return $this->hasMany('App\CategoryResult');
    }
}
