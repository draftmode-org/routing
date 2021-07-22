<?php
namespace singleframe\Routing\Exception;
use RuntimeException;

class RouteClassMethodNotFoundException extends RuntimeException {
    public function __construct(string $uriClass, string $uriMethod) {
        parent::__construct("method for $uriMethod in uriClass $uriClass found", 404);
    }
}