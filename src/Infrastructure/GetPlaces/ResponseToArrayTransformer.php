<?php

namespace TerePlaces\Infrastructure\GetPlaces;

use TerePlaces\Domain\GetPlacesDataTransformer;

class ResponseToArrayTransformer implements GetPlacesDataTransformer
{
    public function transform(array $response): array
    {
        return array_map(function ($aPlace) {
            return [
                'address' => $aPlace['formatted_address'],
                'latitude' => $aPlace['geometry']['location']['lat'],
                'longitude' => $aPlace['geometry']['location']['lng'],
                'name' => $aPlace['name'],
                'types' => implode('|', $aPlace['types']),
            ];
        }, $response['results']);
    }
}
