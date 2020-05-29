<?php

namespace Laraveles\Rating\Exception;

use Exception;

class InvalidScore extends Exception
{
    public function __construct()
    {
        parent::__construct(trans('rating::rating.invalidScore', [
            'from' => config('rating.from'),
            'to' => config('rating.to'),
        ]));
    }

    public function render()
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], 422);
    }
}
