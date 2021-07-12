<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'integer',
    ];

    public function experiments () {
        return $this->hasMany(Experiment::class);
    }

    public function scientist_request () {
        return $this->hasOne(ScientistRequest::class);
    }

    # pivot table
    public function picture_sets () {
        return $this->belongsToMany(PictureSet::class);
    }

    # pivot table
    public function invited_experiments () {
        return $this->belongsToMany(Experiment::class);
    }
}
