<?php

namespace Terrazza\Component\Routing;

interface RouteMatcherInterface {
    /**
     * @param RouteSearchClass $routeSearch
     * @return Route|null
     */
    public function match(RouteSearchClass $routeSearch) :?Route;
}