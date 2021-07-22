<?php
namespace singleframe\Routing\RouteBuilder;

use singleframe\Routing\Exception\RouteCollectionClassBuilderException;
use singleframe\Routing\IRouteClassBuilder;
use singleframe\Routing\IRouteClassMethodBuilder;
use singleframe\Routing\Route;
use singleframe\Routing\RouteCollection;
use Throwable;

class RouteClassBuilder implements IRouteClassBuilder {
    private RouteCollection $routes;
    private IRouteClassMethodBuilder $routeMethodBuilder;

    public function __construct(RouteCollection $routes, IRouteClassMethodBuilder $routeMethodBuilder) {
        $this->routes                               = $routes;
        $this->routeMethodBuilder                   = $routeMethodBuilder;
    }

    /**
     * @param string $routeConfigFile
     * @param IRouteClassMethodBuilder $routeMethodBuilder
     * @return IRouteClassBuilder
     */
    public static function buildFromFile(string $routeConfigFile, IRouteClassMethodBuilder $routeMethodBuilder) : IRouteClassBuilder {
        try {
            if (file_exists($routeConfigFile)) {
                $routes                             = require_once($routeConfigFile);
                if (is_array($routes)) {
                    // validate Interface
                    foreach ($routes as $route) {
                        if (!($route instanceof Route)) {
                            throw new RouteCollectionClassBuilderException("routeConfigFile requires array of Route elements, given ".is_object($route) ? get_class($route) : gettype($route), 500);
                        }
                    }
                    // return collection
                    return new self(
                        new RouteCollection(...$routes),
                        $routeMethodBuilder
                    );
                } else {
                    throw new RouteCollectionClassBuilderException("routeConfigFile $routeConfigFile does not response an array", 500);
                }
            } else {
                throw new RouteCollectionClassBuilderException("routeConfigFile $routeConfigFile could not be found", 500);
            }
        } catch (Throwable $exception) {
            throw new RouteCollectionClassBuilderException($exception->getMessage(), 500, $exception);
        }
    }

    /**
     * @return RouteCollection
     */
    public function getClassRoutes() : RouteCollection {
        return $this->routes;
    }

    /**
     * @param Route $parentRoute
     * @return RouteCollection
     */
    public function getClassMethodRoutes(Route $parentRoute) : RouteCollection {
        return $this->routeMethodBuilder->getRoutes($parentRoute);
    }
}