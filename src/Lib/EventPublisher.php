<?php

namespace TerePlaces\Lib;

interface EventPublisher
{
    public function publish($eventName);
}
