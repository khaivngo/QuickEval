<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Picture extends Model
{
    protected $fillable = [
        'name',
        'path',
        'is_original',
        'picture_set_id'
    ];

    // public function paired_result () {
    //   return $this->hasMany('App\PairedResult', 'picture_id_selected', 'id');
    // }
}
