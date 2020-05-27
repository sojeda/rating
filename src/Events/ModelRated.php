<?php

namespace Laraveles\Rating\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laraveles\Rating\Contracts\Qualifier;
use Laraveles\Rating\Contracts\Rateable;

class ModelRated
{
    use Dispatchable, SerializesModels;

    /** @var Qualifier */
    private Qualifier $qualifier;
    /** @var Rateable */
    private Rateable $rateable;
    private float $score;

    public function __construct(Qualifier $qualifier, Rateable $rateable, float $score)
    {
        $this->qualifier = $qualifier;
        $this->rateable = $rateable;
        $this->score = $score;
    }

    /**
     * @return Qualifier
     */
    public function getQualifier(): Qualifier
    {
        return $this->qualifier;
    }

    /**
     * @return Rateable
     */
    public function getRateable(): Rateable
    {
        return $this->rateable;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }
}
