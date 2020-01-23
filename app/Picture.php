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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'id' => 'integer',
    //     'is_original' => 'integer',
    //     'picture_set_id' => 'integer'
    // ];

    // public function paired_result () {
    //   return $this->hasMany('App\PairedResult', 'picture_id_selected', 'id');
    // }
}
