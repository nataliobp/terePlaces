<?php

namespace Test\Application\GetPlacesData;


use TerePlaces\Lib\EventPublisher;

class DummyPublisher implements EventPublisher
{
    public function publish($eventName)
    {
    }
}