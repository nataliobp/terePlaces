<?php

namespace TerePlaces\Application\GetPlacesData;

class GetPlacesDataCommand
{
    private $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function search()
    {
        return $this->search;
    }
}
