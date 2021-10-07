<?php
namespace Terrazza\Component\Routing\Exception;
use RuntimeException;

class RouteClassNotFoundException extends RuntimeException {
    public function __construct(string $uri) {
        parent::__construct("routeClass for uri $uri not found", 404);
    }
}