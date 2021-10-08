<?php

namespace Terrazza\Component\Routing;

interface RouteMatcherInterface {
    /**
     * @param RouteSearchClass $routeSearch
     * @param array|Route[] $routes
     * @param bool $hasMethods
     * @return RouteMatcherFoundClass|null
     */
    public function getRoute(RouteSearchClass $routeSearch, array $routes, bool $hasMethods) :?RouteMatcherFoundClass;
}