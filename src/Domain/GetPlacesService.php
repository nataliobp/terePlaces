<?php

namespace TerePlaces\Domain;

interface GetPlacesService
{
    public function get(array $params): array;
}
