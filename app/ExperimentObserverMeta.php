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

  public function experiment () {
    return $this->belongsTo(Experiment::class);
  }
}
