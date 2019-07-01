<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PictureSet extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description'
    ];

    // public function experiments () {
    //     return $this->hasMany('App\Picture');
    // }
}
