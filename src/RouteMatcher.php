<?php

namespace Terrazza\Component\Routing;

class RouteMatcher implements RouteMatcherInterface {
    /**
     * @var RouteCollectionInterface
     */
    private RouteCollectionInterface $routes;

    public function __construct(RouteCollectionInterface $routes) {
        $this->routes                              = $routes;
    }

    /**
     * @param RouteSearchClass $routeSearch
     * @return Route|null
     */
    public function match(RouteSearchClass $routeSearch) :?Route {
        $useRouteMatch                              = null;
        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($routeMatch = $this->matchRoute($route, $routeSearch)) {
                $useRouteMatch                      = $this->getBestRouteMatch($routeMatch, $useRouteMatch);
            }
        }
        if ($useRouteMatch) {
            return $useRouteMatch->getRoute();
        }
        return null;
    }

    protected function getBestRouteMatch(RouteFoundClass $routeMatch, RouteFoundClass $lastRouteMatch=null) : RouteFoundClass {
        if ($lastRouteMatch) {
            if ($lastRouteMatch->getPreMatchPosition() > $routeMatch->getPreMatchPosition()) {
                return $lastRouteMatch;
            }
        }
        return $routeMatch;
    }

    protected function matchRoute(RouteInterface $route, RouteSearchClass $routeSearch) :?RouteFoundClass {
        if ($route->hasMethod($routeSearch->getSearchMethod())) {
            if ($routeMatch = $route->getMatchedRoute($routeSearch->getSearchUri())) {
                return $routeMatch;
            }
        }
        return null;
    }
}