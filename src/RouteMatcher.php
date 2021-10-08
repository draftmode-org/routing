<?php

namespace Terrazza\Component\Routing;

use ReflectionMethod;
use RuntimeException;
use Throwable;

class RouteMatcher implements RouteMatcherInterface {
    CONST DEFAULT_ANNOTATION                        = "Route";
    CONST ANNOTATION_REGEX                          = '/@%s(?:[ \t]*(.*?))?[ \t]*(?:\*\/)?\r?$/m';

    /**
     * @var string
     */
    private string $annotation;

    public function __construct(string $annotation=null) {
        $this->annotation                           = $annotation ?? self::DEFAULT_ANNOTATION;
    }

    /**
     * @param RouteSearchClass $routeSearch
     * @param array|Route[] $routes
     * @param bool $hasMethods
     * @return RouteMatcherFoundClass|null
     */
    public function getRoute(RouteSearchClass $routeSearch, array $routes, bool $hasMethods=false) :?RouteMatcherFoundClass {
        if ($route = $this->getMatchRoute($routeSearch, $routes)) {
            if ($hasMethods) {
                if ($method = $this->getMethod($routeSearch, $route)) {
                    return new RouteMatcherFoundClass(
                        $route->getRouteClassName(),
                        $method
                    );
                } else {
                    return null;
                }
            } else {
                return new RouteMatcherFoundClass(
                    $route->getRouteClassName()
                );
            }
        } else {
            return null;
        }
    }

    /**
     * @param RouteSearchClass $routeSearch
     * @param array $routes
     * @return Route|null
     */
    private function getMatchRoute(RouteSearchClass $routeSearch, array $routes) :?Route {
        $useRouteMatch                              = null;
        foreach ($routes as $route) {
            if ($routeMatch = $this->matchRoute($route, $routeSearch)) {
                $useRouteMatch                      = $this->getBestRouteMatch($routeMatch, $useRouteMatch);
            }
        }
        if ($useRouteMatch) {
            return $useRouteMatch->getRoute();
        }
        return null;
    }


    /**
     * @param RouteSearchClass $routeSearch
     * @param Route $route
     * @return string|null
     * @throws RuntimeException|null
     */
    private function getMethod(RouteSearchClass $routeSearch, Route $route) :?string {
        $rClassName                                 = $route->getRouteClassName();
        try {
            $rClass                                 = new \ReflectionClass($rClassName);
            $useRouteMatch                          = null;
            foreach ($rClass->getMethods() as $method) {
                if (!$method->isPublic()) continue;
                if ($methodRoute = $this->getMethodRoute($route, $method)) {
                    if ($routeMatch = $this->matchRoute($methodRoute, $routeSearch)) {
                        $useRouteMatch              = $this->getBestRouteMatch($routeMatch, $useRouteMatch);
                    }
                }
            }
            if ($useRouteMatch) {
                return $useRouteMatch->getRoute()->getRouteClassName();
            }
            return null;
        } catch (Throwable $exception) {
            throw new RuntimeException("getMethod failure", $exception->getCode(), $exception);
        }
    }

    /**
     * @param RouteFoundClass $routeMatch
     * @param RouteFoundClass|null $lastRouteMatch
     * @return RouteFoundClass
     */
    private function getBestRouteMatch(RouteFoundClass $routeMatch, RouteFoundClass $lastRouteMatch=null) : RouteFoundClass {
        if ($lastRouteMatch) {
            if ($lastRouteMatch->getPreMatchPosition() > $routeMatch->getPreMatchPosition()) {
                return $lastRouteMatch;
            }
        }
        return $routeMatch;
    }

    /**
     * @param RouteInterface $route
     * @param RouteSearchClass $routeSearch
     * @return RouteFoundClass|null
     */
    private function matchRoute(RouteInterface $route, RouteSearchClass $routeSearch) :?RouteFoundClass {
        if ($route->hasMethod($routeSearch->getSearchMethod())) {
            if ($routeMatch = $route->getMatchedRoute($routeSearch->getSearchUri())) {
                return $routeMatch;
            }
        }
        return null;
    }

    /**
     * @param Route $route
     * @param ReflectionMethod $method
     * @return Route|null
     */
    private function getMethodRoute(Route $route, ReflectionMethod $method) :?Route {
        $uri                                        = null;
        $methods                                    = null;
        if ($subject = $method->getDocComment()) {
            $pattern                                = sprintf(self::ANNOTATION_REGEX, preg_quote($this->annotation, "/"));
            if (preg_match_all($pattern, $subject, $matches)) {
                foreach ($matches[1] as $match) {
                    list($argument, $value)         = explode(" ", $match, 2);
                    switch (trim($argument)) {
                        case "/uri":
                            $value                  = trim($value, "/");
                            $uri                    = $route->getRouteUri() . "/" . $value;
                            break;
                        case "/method":
                            $methods                = explode(",", trim($value));
                            break;
                    }
                }
            }
        }
        if ($uri) {
            return new Route($uri, $method->getName(), $methods);
        } else {
            return null;
        }
    }
}