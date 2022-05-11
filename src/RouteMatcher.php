<?php
namespace Terrazza\Component\Routing;

use Psr\Log\LoggerInterface;

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
    //private string $annotation;

    public function __construct(LoggerInterface $logger) {
        $this->logger                               = $logger;
        //$this->annotation                           = $annotation ?? self::DEFAULT_ANNOTATION;
    }

    public function getRoute(RouteSearch $routeSearch, array $routes) :?Route {
        return null;
    }

    /**
     * @param RouteSearch $routeSearch
     * @param Route $route
     * @return Route|null
     */
    public function routeMatch(RouteSearch $routeSearch, Route $route) : ?Route {
        $this->logger->debug("search for", [
            "searchUri"                             => $routeSearch->getUri(),
            "searchMethod"                          => $routeSearch->getMethod(),
            "findUri"                               => $route->getUri(),
            "findMethod"                            => $route->getMethod()]);
        if ($route->getMethod() === $routeSearch->getMethod()) {
            $searchUri                              = trim($routeSearch->getUri(), "/");
            $findUri                                = trim($route->getUri(), "/");

            $staticUriLen                           = strpos($findUri, "{");
            $staticUriLen                           = ($staticUriLen === false) ? strlen($findUri) : $staticUriLen;

            if ($searchUri === $findUri) {
                $this->logger->debug("searchUri identically findUri");
                return $route;
            }

            $pattern                                = '^' . preg_replace('#\{[\w\_]+\}#', '(.+?)', $findUri);
            $pattern                                .= "$";

            if (preg_match("~".$pattern."~", $searchUri, $matches)) {
                if (count($matches) > 1) {
                    $lKey                           = count($matches)-1;
                    if (strpos($matches[$lKey], "/") !== false) {
                        $this->logger->debug("last optional argument includes / and will be ignored", [
                            "searchUri" => $searchUri,
                            "findUri" => $findUri,
                            "matches" => $matches]);
                        return null;
                    }
                }
                $this->logger->debug("Match found",[
                    "staticUriLen"  => $staticUriLen]);
                return $route;
            }
        } else {
            $this->logger->debug("routeMethod(s) does not match searchMethod");
        }
        return null;
    }
/*
    public function getRoute(RouteSearch $routeSearch, array $routes) :?Route {
        $this->logger->debug("find route for ".$routeSearch->getUri().", method: ".$routeSearch->getMethod(), [
            "line" => __LINE__, "method" => __METHOD__]);
        try {
            foreach ($routes as $route) {
                if ($classRoute = $this->routeMatch($routeSearch, $route, true)) {
                    if ($classMethodRoute = $this->getClassMethodMatch($routeSearch, $classRoute->getRoute())) {
                        return $classMethodRoute;
                    } else {
                        $this->logger->debug("classRoute found, classMethodRoute not found", [
                            "line" => __LINE__, "method" => __METHOD__]);
                    }
                }
            }
            $this->logger->debug("classRoute not found", [
                "line" => __LINE__, "method" => __METHOD__]);
            return null;
        } catch (Throwable $exception) {
            $this->logger->error("getRoute unexpected exception, ".$exception->getMessage(), [
                "exception" => $exception,
                "line" => __LINE__, "method" => __METHOD__]);
            return null;
        }
    }

    private function getClassMethodMatch(RouteSearch $routeSearch, Route $classRoute) :?Route {
        $routes                                     = $this->getClassMethodRoutes($classRoute);
        $foundRoute                                 = null;
        foreach ($routes as $route) {
            if ($routeFound = $this->routeMatch($routeSearch, $route, false)) {
                $foundRoute                         = $this->getBestRouteMatch($routeFound, $foundRoute);
            }
        }
        return $foundRoute ? $foundRoute->getRoute() : null;
    }

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

    private function getBestRouteMatch(RouteFound $routeMatch, RouteFound $lastRouteMatch=null) : RouteFound {
        if ($lastRouteMatch && $lastRouteMatch->getStaticUriLen() > $routeMatch->getStaticUriLen()) {
            $this->logger->debug("last routeMatch preferred", [
                "routeStaticUriLen"                 => $routeMatch->getStaticUriLen(),
                "lastRouteStaticUriLen"             => $lastRouteMatch->getStaticUriLen(),
                "line" => __LINE__, "method" => __METHOD__]);
            return $lastRouteMatch;
        }
        return $routeMatch;
    }
*/
}