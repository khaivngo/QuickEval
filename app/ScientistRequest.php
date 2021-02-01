<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScientistRequest extends Model
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
      'user_id' => 'integer',
      'accepted' => 'integer',
  ];

  public function user () {
    return $this->belongsTo(User::class);
  }
}
