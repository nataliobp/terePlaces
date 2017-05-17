<?php

namespace TerePlaces\Infrastructure\GetPlaces;

use TerePlaces\Domain\GetPlacesService;

class DummyService implements GetPlacesService
{
    public function get(array $params): array
    {
        $response = require_once __DIR__ . '/DummyResponse.php';

        return json_decode($response, true);
    }
}
