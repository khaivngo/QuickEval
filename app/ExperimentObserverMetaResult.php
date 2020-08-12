<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExperimentObserverMetaResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function observer_meta () {
        return $this->hasOne(ObserverMeta::class, 'id', 'observer_meta_id');
    }
}
