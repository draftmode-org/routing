<?php

namespace Terrazza\Component\Routing;

use Psr\Log\LoggerInterface;
use ReflectionMethod;
use RuntimeException;
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
        @unlink("log.txt");
        $this->annotation                           = $annotation ?? self::DEFAULT_ANNOTATION;
    }

    /**
     * @param RouteSearchClass $routeSearch
     * @param array|Route[] $routes
     * @param bool $hasMethods
     * @return RouteMatcherFound|null
     */
    public function getRoute(RouteSearchClass $routeSearch, array $routes) :?RouteMatcherFound {
        $this->logger->debug("getRoute for uri: ".$routeSearch->getSearchUri().", method: ".$routeSearch->getSearchMethod(), ["line" => __LINE__, "method" => __METHOD__]);
        if ($route = $this->getMatchRoute($routeSearch, $routes)) {

            $this->logger->debug("route found for ".$route->getRouteClassName().", evaluate methods", ["line" => __LINE__, "method" => __METHOD__]);

            if ($methodFound = $this->getMethod($routeSearch, $route)) {
                $this->logger->debug("route found, method found", ["line" => __LINE__, "method" => __METHOD__]);
                return new RouteMatcherFound(
                    $methodFound->getRoute()->getRouteUri(),
                    $route->getRouteClassName(),
                    $methodFound->getRoute()->getRouteClassName()
                );
            } else {
                $this->logger->debug("route found, method not found", ["line" => __LINE__, "method" => __METHOD__]);
                return null;
            }
        } else {
            $this->logger->debug("route not found", ["line" => __LINE__, "method" => __METHOD__]);
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
            if ($routeMatch = $this->getMatchedRoute($route, $routeSearch, true)) {
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
     * @return RouteFoundClass|null
     * @throws RuntimeException|null
     */
    private function getMethod(RouteSearchClass $routeSearch, Route $route) :?RouteFoundClass {
        $rClassName                                 = $route->getRouteClassName();
        try {
            $rClass                                 = new \ReflectionClass($rClassName);
            $useRouteMatch                          = null;
            foreach ($rClass->getMethods() as $method) {
                if (!$method->isPublic()) continue;
                $this->logger->notice("try to match method in class", [
                        "className"     => $rClassName,
                        "methodName"    => $method->getName(),
                        "method" => __METHOD__, "line" => __LINE__]);
                if ($methodRoute = $this->getMethodRoute($route, $method)) {
                    if ($routeMatch = $this->getMatchedRoute($methodRoute, $routeSearch, false)) {
                        $useRouteMatch              = $this->getBestRouteMatch($routeMatch, $useRouteMatch);
                    }
                }
            }
            return $useRouteMatch;
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
     * @param bool $endOptional
     * @return RouteFoundClass|null
     */
    private function getMatchedRoute(RouteInterface $route, RouteSearchClass $routeSearch, bool $endOptional=false) :?RouteFoundClass {
        if (!$route->hasMethod($routeSearch->getSearchMethod())) {
            $this->logger->notice("routeMethods does not match searchMethod", [
                "routeMethods" => $route->getMethod(),
                "searchMethod" => $routeSearch->getSearchMethod(),
                "method" => __METHOD__, "line" => __LINE__]);
            return null;
        }
        $uri                                        = $routeSearch->getSearchUri();
        $this->logger->notice("search for ".$route->getRouteUri(). " in ".$uri, [
            "method" => __METHOD__, "line" => __LINE__]);
        $uri                                        = trim($uri, "/");
        $routePath                                  = trim($route->getRouteUri(), "/");
        $preMatchPosition                           = 0;
        if (preg_match_all('#\{([\w\_]+)\}#', $routePath, $matches, PREG_OFFSET_CAPTURE)) {
            $preMatchPosition                       = $matches[1][0][1];
        }
        if (strlen($routePath) === 0 && strlen($uri) === 0) {
            return new RouteFoundClass($route, 0);
        }
        $rRoutePath                                 = $routePath;
        $routePath                                  = '^' . preg_replace('#\{[\w\_]+\}#', '(.+?)', $routePath);
        $routePath                                  .= $endOptional ? "(.*?)" : "$";
        if (preg_match("~".$routePath."~", trim($uri, "/"), $matches)) {
            if (strpos($matches[1] ?? "", "/") !== false) {
                $this->logger->notice("optional arguments include includes /", [
                    "routePath" => $routePath,
                    "uri" => trim($uri, "/"),
                    "matches" => $matches,
                    "method" => __METHOD__, "line" => __LINE__]);
                return null;
            }
            $this->logger->notice("found match", [
                "routePath" => $routePath,
                "uri" => trim($uri, "/"),
                "method" => __METHOD__, "line" => __LINE__]);
            return new RouteFoundClass($route, $preMatchPosition);
        } else {
            if (strlen($rRoutePath) && $uri === $rRoutePath) {
                $this->logger->notice("found match, uri == routePath", [
                    "routePath" => $routePath,
                    "uri" => trim($uri, "/"),
                    "method" => __METHOD__, "line" => __LINE__]);
                return new RouteFoundClass($route, 0);
            }
            $this->logger->notice("no match found", [
                "routePath" => $routePath,
                "uri" => trim($uri, "/"),
                "method" => __METHOD__, "line" => __LINE__]);
            return null;
        }
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