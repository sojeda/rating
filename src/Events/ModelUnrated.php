<?php

namespace Laraveles\Rating\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laraveles\Rating\Contracts\Qualifier;
use Laraveles\Rating\Contracts\Rateable;

class ModelUnrated
{
    use Dispatchable, SerializesModels;

    private Rateable $rateable;
    /** @var Qualifier */
    private Qualifier $qualifier;

    public function __construct(Qualifier $qualifier, Rateable $rateable)
    {
        $this->rateable = $rateable;
        $this->qualifier = $qualifier;
    }

    /**
     * @return Rateable
     */
    public function getRateable(): Rateable
    {
        return $this->rateable;
    }

    /**
     * @return Qualifier
     */
    public function getQualifier(): Qualifier
    {
        return $this->qualifier;
    }
}
