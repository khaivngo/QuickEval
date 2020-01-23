<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Experiment extends Model
{
    protected $fillable = [
        'user_id',
        'first_version_id',
        'version',
        'title',
        'experiment_type_id',
        'short_description',
        'long_description',
        'picture_sequence_algorithm',
        'delay',
        'stimuli_spacing',
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

    // protected $casts = [
    //     'id' => 'integer',
    //     'user_id' => 'integer',
    //     'first_version_id' => 'integer',
    //     'version' => 'integer',
    //     'experiment_type_id' => 'integer',
    //     'picture_sequence_algorithm' => 'integer',
    //     'delay' => 'integer',
    //     'stimuli_spacing' => 'integer',
    //     'is_public' => 'integer',
    //     'allow_colour_blind' => 'integer',
    //     'timer' => 'integer',
    //     'allow_ties' => 'integer',
    //     'show_original' => 'integer',
    //     'same_pair' => 'integer',
    //     'horizontal_flip' => 'integer',
    //     'natural_lighting' => 'integer'
    // ];
}
