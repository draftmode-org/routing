<?php

use singleframe\Routing\Route;
use singleframe\Tests\Routing\Unit\RouterTestClassController;
use singleframe\Tests\Routing\Unit\RouterTestClassController2;

return [
    new Route("/", RouterTestClassController::class),
    new Route("/test", RouterTestClassController2::class),
];