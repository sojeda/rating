<?php

namespace Laraveles\Rating\Exception;

use Exception;

class InvalidScoreRating extends Exception
{
    public function render()
    {
        return response()->json([
            'error' => $this->getMessage(),
        ], 422);
    }
}
