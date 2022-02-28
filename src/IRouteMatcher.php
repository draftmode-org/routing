<?php

namespace Terrazza\Component\Routing;

interface IRouteMatcher {
    /**
     * @param RouteSearchClass $routeSearch
     * @param array|Route[] $routes
     * @return RouteMatcherFound|null
     */
    public function getRoute(RouteSearchClass $routeSearch, array $routes) :?RouteMatcherFound;
}