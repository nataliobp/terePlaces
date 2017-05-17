<?php

namespace Test\Application\GetPlacesData;

use PHPUnit\Framework\TestCase;
use TerePlaces\Application\GetPlacesData\DummyGetPlacesDataCommand;
use TerePlaces\Application\GetPlacesData\GetPlacesDataCommandHandler;
use TerePlaces\Infrastructure\GetPlaces\DummyService;
use TerePlaces\Infrastructure\GetPlaces\ResponseToArrayTransformer;

class GetPlacesDataCommandHandlerTest extends TestCase
{
    const AN_EMPTY_SEARCH = [];
    const A_SEARCH = ['search' => 'aSearch'];

    /** @var  GetPlacesDataCommandHandler */
    private $sut;

    protected function setUp()
    {
        $this->sut = new GetPlacesDataCommandHandler(
            new DummyService(),
            new DummyPublisher(),
            new ResponseToArrayTransformer()
        );
    }

    /**
     * @test
     */
    public function givenEmptySearchParamsWhenSearchingThenEmptyArrayIsReturned()
    {
        $result = $this->sut->handle(new DummyGetPlacesDataCommand(self::AN_EMPTY_SEARCH));
        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function givenValidSearchParamasWhenSearchingThenTransformedResultsAreReturned()
    {
        $result = $this->sut->handle(new DummyGetPlacesDataCommand(self::A_SEARCH));

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('address', $result[0]);
        $this->assertArrayHasKey('latitude', $result[0]);
        $this->assertArrayHasKey('longitude', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('types', $result[0]);
    }
}