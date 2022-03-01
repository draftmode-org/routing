<?php

namespace Terrazza\Component\Routing;

interface IRouteMatcher {
    /**
     * @param RouteSearch $routeSearch
     * @param array|Route[] $routes
     * @return Route|null
     */
    public function getRoute(RouteSearch $routeSearch, array $routes) :?Route;
}