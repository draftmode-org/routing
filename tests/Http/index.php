<?php
use singleframe\Http\Message\HttpMessageAdapter;
use singleframe\Injector\Injector;
use singleframe\Log\Logger;
use singleframe\Routing\HttpEntryPoint;
use singleframe\Routing\RouteBuilder\RouteClassBuilder;
use singleframe\Routing\RouteBuilder\RouteClassMethodAnnotationBuilder;

require_once ("../../plugin/autoload.php");
require_once ("../../tests/Unit/RouterTest.php");

$injectorMappingConfig                              = __DIR__ . DIRECTORY_SEPARATOR . ".di.config.php";
$routeClassConfigFile                               = __DIR__ . DIRECTORY_SEPARATOR . ".routes.config.php";
(new HttpEntryPoint(
    new HttpMessageAdapter(),
    new Injector(
        $injectorMappingConfig,
        new Logger()
    ),
    RouteClassBuilder::buildFromFile(
        $routeClassConfigFile,
        new RouteClassMethodAnnotationBuilder()
    )
))->process();
