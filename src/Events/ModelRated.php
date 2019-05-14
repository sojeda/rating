<?php

namespace Laraveles\Rating\Events;

use Illuminate\Database\Eloquent\Model;

class ModelRated
{
    /**
     * @var Model
     */
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
    }

}
