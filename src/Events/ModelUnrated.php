<?php

namespace Laraveles\Rating\Events;

use Illuminate\Database\Eloquent\Model;

class ModelUnrated
{
    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
