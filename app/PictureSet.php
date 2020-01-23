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

    // protected $casts = [
    //     'id' => 'integer',
    //     'user_id' => 'integer',
    //     'picture_set_id' => 'integer'
    // ];

    // public function experiments () {
    //     return $this->hasMany('App\Picture');
    // }
}
