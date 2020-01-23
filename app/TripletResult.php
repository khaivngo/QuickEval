<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TripletResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'category_id_left',
        'category_id_middle',
        'category_id_right',
        'picture_id_left',
        'picture_id_middle',
        'picture_id_right',
        'chose_none'
    ];

    // protected $casts = [
    //     'id' => 'integer',
    //     'experiment_result_id' => 'integer',
    //     'picture_id_left' => 'integer',
    //     'picture_id_middle' => 'integer',
    //     'picture_id_right' => 'integer',
    //     'chose_none' => 'integer'
    // ];

    public function experiment_result () {
        return $this->belongsTo('App\ExperimentResult');
    }

    public function category_left   () { return $this->belongsTo('App\Category', 'category_id_left');   }
    public function category_middle () { return $this->belongsTo('App\Category', 'category_id_middle'); }
    public function category_right  () { return $this->belongsTo('App\Category', 'category_id_right');  }

    public function picture_left    () { return $this->belongsTo('App\Picture', 'picture_id_left');   }
    public function picture_middle  () { return $this->belongsTo('App\Picture', 'picture_id_middle'); }
    public function picture_right   () { return $this->belongsTo('App\Picture', 'picture_id_right');  }
}
