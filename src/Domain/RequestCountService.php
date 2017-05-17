<?php

namespace TerePlaces\Domain;

interface RequestCountService
{
    public function getCountFromToday(): int;
    public function incrementCountFromToday();
}
