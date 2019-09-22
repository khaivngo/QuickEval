<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScientistRequest extends Model
{
    protected $fillable = [
        'user_id',
        'accepted'
    ];

    public function user () {
        // return $this->hasOne('App\User');
        return $this->belongsTo('App\User');
    }
}
