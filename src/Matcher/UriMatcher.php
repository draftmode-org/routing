<?php
namespace Terrazza\Component\Routing\Matcher;

use Terrazza\Http\Request\IHttpRequest;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteCollection;
use Terrazza\Component\Routing\RouteMatch;

class UriMatcher {
    private RouteCollection $routes;
    public function __construct(RouteCollection $routes) {
        $this->routes                               = $routes;
    }

    public function setRoutes(RouteCollection $routes) : self {
        $this->routes                               = $routes;
        return $this;
    }

    public function match(IHttpRequest $request) :?Route {
        $useRouteMatch                              = null;
        foreach ($this->routes as $route) {
            if ($routeMatch = $this->matchRoute($route, $request)) {
                $useRouteMatch                      = $this->getBestRouteMatch($routeMatch, $useRouteMatch);
            }
        }
        if ($useRouteMatch) {
            return $useRouteMatch->getRoute();
        }
        return null;
    }

    public function searchUri(IHttpRequest $request) : string {
        return $request->getUri()->getPath();
    }

    protected function getBestRouteMatch(RouteMatch $routeMatch, RouteMatch $lastRouteMatch=null) : RouteMatch {
        if ($lastRouteMatch) {
            if ($lastRouteMatch->getPreMatchPosition() > $routeMatch->getPreMatchPosition()) {
                return $lastRouteMatch;
            }
        }
        return $routeMatch;
    }

    protected function matchRoute(Route $route, IHttpRequest $request) :?RouteMatch {
        if ($route->hasMethod($request->getMethod())) {
            if ($routeMatch = $route->hasRoute($this->searchUri($request))) {
                return $routeMatch;
            }
        }
        return null;
    }
}