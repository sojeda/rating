<?php

namespace Laraveles\Rating\Contracts;

interface Rateable
{
    public function averageRating(): float;

    public function getKey();

    public function name(): string;

    public function qualifications();

    public function hasRateBy(Qualifier $model): bool;
}
