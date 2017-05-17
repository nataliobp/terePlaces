<?php

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use TerePlaces\Application\GetPlacesData\DummyGetPlacesDataCommand;
use TerePlaces\Application\GetPlacesData\GetPlacesDataCommand;
use TerePlaces\Application\GetPlacesData\GetPlacesDataCommandHandler;
use TerePlaces\Infrastructure\CountService\PdoCountService;
use TerePlaces\Infrastructure\Excell\ArrayToExcellTransformer;
use TerePlaces\Infrastructure\GetPlaces\DummyService;
use TerePlaces\Infrastructure\GetPlaces\GoogleApiService;
use TerePlaces\Infrastructure\GetPlaces\ResponseToArrayTransformer;
use TerePlaces\Infrastructure\Publisher\LeaugueEmitter;

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app['config'] = function () {
    return Yaml::parse(file_get_contents(__DIR__.'/app/config/config.yml'));
};

$app['twig'] = function () {
    $loader = new Twig_Loader_Filesystem(__DIR__.'/app/resources/templates');

    return new Twig_Environment($loader);
};

$app['connection'] = function () use ($app) {
    $pdo = new \PDO(
        sprintf('mysql:host=%s;dbname=%s', $app['config']['mysql']['hostname'], $app['config']['mysql']['database']),
        $app['config']['mysql']['username'],
        $app['config']['mysql']['password'],
        [\PDO::ATTR_PERSISTENT => true]
    );

    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    return $pdo;
};

$app['command_bus'] = function () use ($app) {
    return League\Tactician\Setup\QuickStart::create([
        GetPlacesDataCommand::class => $app['get_places_data_command_handler'],
        DummyGetPlacesDataCommand::class => $app['dumy_get_places_data_command_handler'],
    ]);
};

$app['publisher'] = function () use ($app) {
    $emitter = new \League\Event\Emitter();

    $emitter->addListener('request.sent', function () use ($app) {
        $app['count_service']->incrementCountFromToday();
    });

    return new LeaugueEmitter($emitter);
};

$app['count_service'] = function () use ($app) {
    return new PdoCountService($app['connection']);
};

$app['response_to_array_transformer'] = function () {
    return new ResponseToArrayTransformer();
};

$app['array_to_excell_transformer'] = function () {
    return new ArrayToExcellTransformer();
};

$app['google_api_get_places_service'] = function () use ($app) {
    return new GoogleApiService(
        new Client(),
        $app['config']['maps_api_key']
    );
};

$app['dummy_get_places_service'] = function () use ($app) {
    return new DummyService();
};

$app['get_places_data_command_handler'] = function () use ($app) {
    return new GetPlacesDataCommandHandler(
        $app['google_api_get_places_service'],
        $app['publisher'],
        $app['response_to_array_transformer']
    );
};

$app['dumy_get_places_data_command_handler'] = function () use ($app) {
    return new GetPlacesDataCommandHandler(
        $app['dummy_get_places_service'],
        $app['publisher'],
        $app['response_to_array_transformer']
    );
};

$app->get('/', function (Request $request) use ($app) {
    $params = array_filter($request->query->all());
    $search = [];

    if (!empty($params['search'])) {
        $search['query'] = $params['search'];
    }

    if (!empty($params['latitude']) && !empty($params['longitude']) && !empty($params['radio'])) {
        $search['location'] = $params['latitude'].','.$params['longitude'];
        $search['radio'] = $params['radio'];
    }

    if (!empty($params['types'])) {
        $search['types'] = implode('|', $params['types']);
    }

    $command = $request->query->get('debug') ? DummyGetPlacesDataCommand::class : GetPlacesDataCommand::class;
    try {
        return $app['twig']->render('index.html.twig', [
        'params' => $params,
        'places' => $app['command_bus']->handle(new $command($search)),
        'count' => $app['count_service']->getCountFromToday(),
    ]);
    } catch (\Throwable $t) {
        return $t->getMessage();
    }
});

$app->post('download/excell', function (Request $request) use ($app) {
    if (empty($request->get('data'))) {
        return 'No values to transform into excell';
    }

    $content = $app['array_to_excell_transformer']->transform(json_decode($request->get('data'), true));
    $filename = (new \DateTime())->format('Y-m-d His').'.xls';

    $response = new Response();
    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
    $response->headers->set('Content-Disposition', " attachment; filename=\"$filename\"");
    $response->setContent($content);

    return $response;
});
$app->run();
