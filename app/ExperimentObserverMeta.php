<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentObserverMeta extends Model
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
      'experiment_id' => 'integer',
      'observer_meta_id' => 'integer',
  ];

  public function experiment () {
    return $this->belongsTo(Experiment::class);
  }

  public function observer_meta () {
    return $this->belongsTo(ObserverMeta::class);
  }
}
