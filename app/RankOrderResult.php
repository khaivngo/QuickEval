<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RankOrderResult extends Model
{
    protected $fillable = [
        'experiment_result_id',
        'picture_set_id',
        'picture_id',
        'ranking'
    ];

    public function experiment_result () {
        return $this->belongsTo('App\ExperimentResult');
    }

    // public function category_left   () { return $this->belongsTo('App\Category', 'category_id_left');   }
    // public function category_middle () { return $this->belongsTo('App\Category', 'category_id_middle'); }
    // public function category_right  () { return $this->belongsTo('App\Category', 'category_id_right');  }

    // public function picture_left    () { return $this->belongsTo('App\Picture', 'picture_id_left');   }
    // public function picture_middle  () { return $this->belongsTo('App\Picture', 'picture_id_middle'); }
    // public function picture_right   () { return $this->belongsTo('App\Picture', 'picture_id_right');  }
}
