<?php

namespace Laraveles\Rating\Test\Models;

use Laraveles\Rating\Traits\CanRate;
use Laraveles\Rating\Contracts\Rating;
use Laraveles\Rating\Traits\CanBeRated;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Rating
{
    use CanRate, CanBeRated;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
