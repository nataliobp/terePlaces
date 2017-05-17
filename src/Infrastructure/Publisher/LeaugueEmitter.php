<?php

namespace TerePlaces\Infrastructure\Publisher;

use League\Event\Emitter;
use TerePlaces\Lib\EventPublisher;

class LeaugueEmitter implements EventPublisher
{
    private $emitter;

    public function __construct(Emitter $emitter)
    {
        $this->emitter = $emitter;
    }

    public function publish($eventName)
    {
        $this->emitter->emit($eventName);
    }
}
