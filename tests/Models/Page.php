<?php

namespace Laraveles\Rating\Test\Models;

use Laraveles\Rating\Traits\CanRate;
use Laraveles\Rating\Contracts\Rating;
use Laraveles\Rating\Traits\CanBeRated;
use Illuminate\Database\Eloquent\Model;

class Page extends Model implements Rating
{
    use CanRate, CanBeRated;

    protected $fillable = [
        'name',
    ];
}
