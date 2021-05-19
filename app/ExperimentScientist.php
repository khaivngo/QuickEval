<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentScientist extends Model
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
      'user_id' => 'integer',
  ];

  public function experiment () {
    return $this->belongsTo(Experiment::class);
  }

  public function user () {
    return $this->belongsTo(User::class);
  }
}
