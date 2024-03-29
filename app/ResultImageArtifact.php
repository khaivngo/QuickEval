<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultImageArtifact extends Model
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
        'picture_id' => 'integer',
        'client_side_timer' => 'integer',
        // 'selected_area' => 'array'
    ];

    public function experiment_result () {
        return $this->belongsTo(ExperimentResult::class);
    }

    public function picture () {
        return $this->belongsTo(Picture::class, 'picture_id');
    }
}
