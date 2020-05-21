<?php

namespace Laraveles\Rating\Test\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laraveles\Rating\Contracts\Rating;
use Laraveles\Rating\Traits\CanBeRated;
use Laraveles\Rating\Traits\CanRate;

class User extends Authenticatable implements Rating
{
    use CanRate, CanBeRated;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function name(): string
    {
        return $this->name;
    }
}
