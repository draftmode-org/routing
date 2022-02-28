<?php

namespace Terrazza\Component\Routing;

interface RouteInterface
{
    /**
     * @param string $method
     * @return bool
     */
    public function hasMethod(string $method) : bool;

    /**
     * @return array|null
     */
    public function getMethod() : ?array;

    /**
     * @return string
     */
    public function getRouteUri() : string;

    /**
     * @return string
     */
    public function getRouteClassName() : string;
}