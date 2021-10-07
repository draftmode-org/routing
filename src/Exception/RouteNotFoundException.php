<?php
namespace Terrazza\Component\Routing\Exception;
use RuntimeException;

class RouteNotFoundException extends RuntimeException {
    public function __construct(string $requestTarget) {
        parent::__construct("requestTarget not found, given ".$requestTarget, 400);
    }
}