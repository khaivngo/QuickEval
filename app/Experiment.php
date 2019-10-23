<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'experiment_type_id',
        'short_description',
        'long_description',
        'piture_sequence_algorithm',
        'is_public',
        'allow_colour_blind',
        'timer',
        'allow_ties',
        'show_original',
        'same_pair',
        'horizontal_flip',
        'natural_lighting',
        'background_colour',
        'monitor_distance',
        'light_type',
        'viewing_distance',
        'screen_luminance',
        'white_point',
        'white_point_room',
        'ambient_illumination'
    ];
}
