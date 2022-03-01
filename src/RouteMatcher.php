<?php

namespace Terrazza\Component\Routing;

use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

class RouteMatcher implements IRouteMatcher {
    CONST DEFAULT_ANNOTATION                        = "Route";
    CONST ANNOTATION_REGEX                          = '/@%s(?:[ \t]*(.*?))?[ \t]*(?:\*\/)?\r?$/m';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var string
     */
    private string $annotation;

    public function __construct(LoggerInterface $logger, string $annotation=null) {
        $this->logger                               = $logger;
        $this->annotation                           = $annotation ?? self::DEFAULT_ANNOTATION;
    }

    /**
     * @param RouteSearch $routeSearch
     * @param Route[] $routes
     * @return Route|null
     */
    public function getRoute(RouteSearch $routeSearch, array $routes) :?Route {
        $this->logger->debug("find route for ".$routeSearch->getUri().", method: ".$routeSearch->getMethod(), [
            "line" => __LINE__, "method" => __METHOD__]);
        try {
            if ($classRoute = $this->getClassMatch($routeSearch, $routes)) {
                if ($classMethodRoute = $this->getClassMethodMatch($routeSearch, $classRoute)) {
                    return $classMethodRoute;
                } else {
                    $this->logger->debug("classRoute found, classMethodRoute not found", [
                        "line" => __LINE__, "method" => __METHOD__]);
                }
            } else {
                $this->logger->debug("classRoute not found", [
                    "line" => __LINE__, "method" => __METHOD__]);
            }
            return null;
        } catch (Throwable $exception) {
            $this->logger->error("getRoute unexpected exception, ".$exception->getMessage(), [
                "exception" => $exception,
                "line" => __LINE__, "method" => __METHOD__]);
            return null;
        }
    }

    /**
     * @param RouteSearch $routeSearch
     * @param Route[] $routes
     * @return Route|null
     */
    private function getClassMatch(RouteSearch $routeSearch, array $routes) :?Route {
        $foundRoute                                 = null;
        foreach ($routes as $route) {
            if ($routeMatch = $this->routeMatch($routeSearch, $route, true)) {
                $foundRoute                         = $this->getBestRouteMatch($routeMatch, $foundRoute);
                $this->logger->debug("classRoute matches", [
                    "position"                      => $foundRoute->getPosition(),
                    "line" => __LINE__, "method" => __METHOD__]);
            }
        }
        return $foundRoute ? $foundRoute->getRoute() : null;
    }

    /**
     * @param RouteSearch $routeSearch
     * @param Route $classRoute
     * @return Route|null
     * @throws ReflectionException
     */
    private function getClassMethodMatch(RouteSearch $routeSearch, Route $classRoute) :?Route {
        $routes                                     = $this->getClassMethodRoutes($classRoute);
        $foundRoute                                 = null;
        /** @var Route $classMethodRoute */
        foreach ($routes as $route) {
            if ($routeFound = $this->routeMatch($routeSearch, $route, false)) {
                $foundRoute                         = $this->getBestRouteMatch($routeFound, $foundRoute);
                $this->logger->debug("classRoute and classMethodRoute match", [
                    "position"                      => $foundRoute->getPosition(),
                    "line" => __LINE__, "method" => __METHOD__]);
            }
        }
        return $foundRoute ? $foundRoute->getRoute() : null;
    }

    /**
     * @param RouteSearch $routeSearch
     * @param Route $route
     * @param bool $optionalUriEnding
     * @return RouteFound|null
     */
    private function routeMatch(RouteSearch $routeSearch, Route $route, bool $optionalUriEnding) : ?RouteFound {
        $this->logger->debug("search for", [
            "searchUri" => $routeSearch->getUri(),
            "searchMethod" => $routeSearch->getMethod(),
            "findUri" => $route->getUri(),
            "findMethods" => $route->getMethods(),
            "routeClassName" => $route->getClassName(),
            "routeClassMethodName" => $route->getClassMethodName(),
            "method"    => __METHOD__, "line" => __LINE__]);
        if ($route->hasMethod($routeSearch->getMethod())) {
            $searchUri                              = trim($routeSearch->getUri(), "/");
            $findUri                                = trim($route->getUri(), "/");
            if ($searchUri === $findUri) {
                $this->logger->debug("searchUri identically findUri", [
                    "method"    => __METHOD__, "line" => __LINE__]);
                return new RouteFound($route, 0);
            }
            $findArgument                           = strpos($findUri, "{");
            $pattern                                = '^' . preg_replace('#\{[\w\_]+\}#', '(.+?)', $findUri);
            $pattern                                .= $optionalUriEnding ? "(.*?)" : "$";

            if (preg_match("~".$pattern."~", $searchUri, $matches)) {
                if (strpos($matches[1] ?? "", "/") !== false) {
                    $this->logger->debug("optional arguments include includes /, route is ignored", [
                        "searchUri" => $searchUri,
                        "findUri"   => $findUri,
                        "matches"   => $matches,
                        "method"    => __METHOD__, "line" => __LINE__]);
                    return null;
                }
                return new RouteFound($route, $findArgument ?? 0);
            }
        } else {
            $this->logger->debug("routeMethod(s) does not match searchMethod", [
                "method" => __METHOD__, "line" => __LINE__]);
        }
        return null;
    }

    /**
     * @param Route $classRoute
     * @return Route[]
     * @throws ReflectionException
     */
    private function getClassMethodRoutes(Route $classRoute) : array {
        $rClassName                                 = $classRoute->getClassName();
        $routes                                     = [];
        $rClass                                     = new ReflectionClass($rClassName);
        foreach ($rClass->getMethods() as $method) {
            if (!$method->isPublic()) continue;
            if ($route = $this->buildClassMethodRoute($classRoute, $method)) {
                $routes[]                       = $route;
            }
        }
        return $routes;
    }

    /**
     * @param Route $classRoute
     * @param ReflectionMethod $classMethod
     * @return Route|null
     */
    private function buildClassMethodRoute(Route $classRoute, ReflectionMethod $classMethod) :?Route {
        $uri                                        = null;
        $methods                                    = null;
        if ($subject = $classMethod->getDocComment()) {
            $pattern                                = sprintf(self::ANNOTATION_REGEX, preg_quote($this->annotation, "/"));
            if (preg_match_all($pattern, $subject, $matches)) {
                $uri                                = "/";
                foreach ($matches[1] as $match) {
                    list($argument, $value)         = explode(" ", $match, 2);
                    switch (trim($argument)) {
                        case "/uri":
                            $uri                    = trim($value);
                            break;
                        case "/method":
                            $methods                = explode(",", trim($value));
                            break;
                    }
                }
            }
        }
        if ($uri) {
            $this->logger->debug("withMethodFilter", [
                "className"                         => $classRoute->getClassName(),
                "classMethodName"                   => $classMethod->getName(),
                "uri"                               => $uri,
                "methods"                           => $methods,
                "method" => __METHOD__, "line" => __LINE__]);
            return $classRoute->withMethodFilter($uri, $classMethod->getName(), $methods);
        } else {
            $this->logger->debug("method does not have any docComment within @".$this->annotation." annotation", [
                "className"                         => $classRoute->getClassName(),
                "classMethodName"                   => $classMethod->getName(),
                "method" => __METHOD__, "line" => __LINE__]);
            return null;
        }
    }

    /**
     * @param RouteFound $routeMatch
     * @param RouteFound|null $lastRouteMatch
     * @return RouteFound
     */
    private function getBestRouteMatch(RouteFound $routeMatch, RouteFound $lastRouteMatch=null) : RouteFound {
        if ($lastRouteMatch) {
            if ($lastRouteMatch->getPosition() < $routeMatch->getPosition()) {
                return $lastRouteMatch;
            }
        }
        return $routeMatch;
    }
}