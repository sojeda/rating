<?php

namespace Laraveles\Rating\Events;

use Illuminate\Database\Eloquent\Model;

class ModelRated
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function model(): Model
    {
        return $this->model;
    }
}
