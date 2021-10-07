<?php
use Terrazza\Http\Message\HttpMessageAdapter;
use Terrazza\Injector\Injector;
use Terrazza\Log\Logger;
use Terrazza\Component\Routing\HttpEntryPoint;
use Terrazza\Component\Routing\RouteBuilder\RouteClassBuilder;
use Terrazza\Component\Routing\RouteBuilder\RouteClassMethodAnnotationBuilder;

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
