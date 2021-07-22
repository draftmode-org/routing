<?php
namespace singleframe\Routing\Exception;
use RuntimeException;

class RouteMissingParameterException extends RuntimeException {
    public function __construct(string $message) {
        parent::__construct($message, 400);
    }
}