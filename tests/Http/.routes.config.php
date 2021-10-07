<?php

use Terrazza\Component\Routing\Route;
use Terrazza\Tests\Routing\Unit\RouterTestClassController;
use Terrazza\Tests\Routing\Unit\RouterTestClassController2;

return [
    new Route("/", RouterTestClassController::class),
    new Route("/test", RouterTestClassController2::class),
];