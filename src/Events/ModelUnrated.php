<?php

namespace Laraveles\Rating\Events;

use Illuminate\Database\Eloquent\Model;

class ModelUnrated
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
