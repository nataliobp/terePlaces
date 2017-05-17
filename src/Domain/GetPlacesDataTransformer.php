<?php

namespace TerePlaces\Domain;

interface GetPlacesDataTransformer
{
    public function transform(array $response): array;
}
