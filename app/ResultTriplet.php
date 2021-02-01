<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultTriplet extends Model
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
        'experiment_result_id' => 'integer',
        'category_id_left' => 'integer',
        'category_id_middle' => 'integer',
        'category_id_right' => 'integer',
        'picture_id_left' => 'integer',
        'picture_id_middle' => 'integer',
        'picture_id_right' => 'integer',
        'chose_none' => 'integer',
        'client_side_timer' => 'integer',
    ];

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function category_left   () { return $this->belongsTo(Category::class, 'category_id_left');   }
    public function category_middle () { return $this->belongsTo(Category::class, 'category_id_middle'); }
    public function category_right  () { return $this->belongsTo(Category::class, 'category_id_right');  }

    public function picture_left    () { return $this->belongsTo(Picture::class, 'picture_id_left');    }
    public function picture_middle  () { return $this->belongsTo(Picture::class, 'picture_id_middle');  }
    public function picture_right   () { return $this->belongsTo(Picture::class, 'picture_id_right');   }
}
