<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentObserverMeta extends Model
{
  protected $fillable = [
    'experiment_id',
    'observer_meta_id'
  ];

  // protected $casts = [
  //   'id' => 'integer',
  //   'experiment_id' => 'integer',
  //   'observer_meta_id' => 'integer'
  // ];

  public function experiment () {
    return $this->belongsTo('App\Experiment');
  }
}
