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
     * @return string
     */
    public function getRouteUri() : string;

    /**
     * @return string
     */
    public function getRouteClassName() : string;

    /**
     * @param string $uri
     * @return RouteFoundClass|null
     */
    public function getMatchedRoute(string $uri) :?RouteFoundClass;
}