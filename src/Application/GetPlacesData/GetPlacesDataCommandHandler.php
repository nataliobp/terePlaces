<?php

namespace TerePlaces\Application\GetPlacesData;

use TerePlaces\Domain\GetPlacesDataTransformer;
use TerePlaces\Domain\GetPlacesService;
use TerePlaces\Lib\EventPublisher;

class GetPlacesDataCommandHandler
{
    private $transformer;
    private $getPlacesService;
    private $publisher;

    public function __construct(
        GetPlacesService $getPlacesService,
        EventPublisher $publisher,
        GetPlacesDataTransformer $transformer
    ) {
        $this->transformer = $transformer;
        $this->getPlacesService = $getPlacesService;
        $this->publisher = $publisher;
    }

    public function handle(GetPlacesDataCommand $command): array
    {
        if (empty($command->search())) {
            return [];
        }

        $response = $this->getPlacesService->get($command->search());
        $this->publisher->publish('request.sent');

        return $this->transformer->transform($response);
    }
}
