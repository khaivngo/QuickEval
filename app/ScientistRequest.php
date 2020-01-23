<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScientistRequest extends Model
{
  protected $fillable = [
    'user_id',
    'accepted'
  ];

  // protected $casts = [
  //     'id'       => 'integer',
  //     'user_id'  => 'integer',
  //     'accepted' => 'integer'
  // ];

  public function user () {
    return $this->belongsTo('App\User');
  }
}
