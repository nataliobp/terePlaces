<?php

namespace TerePlaces\Infrastructure\GetPlaces;

use GuzzleHttp\Client;
use TerePlaces\Domain\GetPlacesService;

class GoogleApiService implements GetPlacesService
{
    const MAPS_BASE_URL = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

    private $client;
    private $apiKey;

    public function __construct(Client $client, string $apiKey)
    {
        $this->client = $client;
        $this->apiKey = $apiKey;
    }

    public function get(array $params): array
    {
        $response = $this->client->get(
            sprintf('%s?%s', self::MAPS_BASE_URL, http_build_query(array_merge(['key' => $this->apiKey], $params)))
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
