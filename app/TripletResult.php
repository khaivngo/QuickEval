<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TripletResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function category_left   () { return $this->belongsTo(Category::class, 'category_id_left');   }
    public function category_middle () { return $this->belongsTo(Category::class, 'category_id_middle'); }
    public function category_right  () { return $this->belongsTo(Category::class, 'category_id_right');  }

    public function picture_left    () { return $this->belongsTo(Category::class, 'picture_id_left');    }
    public function picture_middle  () { return $this->belongsTo(Category::class, 'picture_id_middle');  }
    public function picture_right   () { return $this->belongsTo(Category::class, 'picture_id_right');   }
}
