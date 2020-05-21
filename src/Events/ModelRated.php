<?php

namespace Laraveles\Rating\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laraveles\Rating\Contracts\Rateable;

class ModelRated
{
    use Dispatchable, SerializesModels;

    private Rateable $model;

    public function __construct(Rateable $model)
    {
        $this->model = $model;
    }

    public function getModel(): Rateable
    {
        return $this->model;
    }
}
