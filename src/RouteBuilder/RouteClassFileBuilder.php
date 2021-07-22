<?php
namespace singleframe\Routing\RouteBuilder;

use singleframe\Routing\Exception\RouteCollectionClassBuilderException;
use singleframe\Routing\IRouteClassBuilder;
use singleframe\Routing\IRouteClassMethodBuilder;
use singleframe\Routing\Route;
use singleframe\Routing\RouteCollection;
use Throwable;

class RouteClassFileBuilder implements IRouteClassBuilder {
    private string $routeConfigFile;
    private IRouteClassMethodBuilder $routeMethodBuilder;

    public function __construct(string $routeConfigFile, IRouteClassMethodBuilder $routeMethodBuilder) {
        $this->routeConfigFile                      = $routeConfigFile;
        $this->routeMethodBuilder                   = $routeMethodBuilder;
    }

    /**
     * @return RouteCollection
     */
    public function getClassRoutes() : RouteCollection {
        try {
            if (file_exists($this->routeConfigFile)) {
                $routes                             = require_once($this->routeConfigFile);
                if (is_array($routes)) {
                    // validate Interface
                    foreach ($routes as $route) {
                        if (!($route instanceof Route)) {
                            throw new RouteCollectionClassBuilderException("routeConfigFile requires array of Route elements, given ".is_object($route) ? get_class($route) : gettype($route), 500);
                        }
                    }
                    // return collection
                    return new RouteCollection(...$routes);
                } else {
                    throw new RouteCollectionClassBuilderException("routeConfigFile ".$this->routeConfigFile." does not response an array", 500);
                }
            } else {
                throw new RouteCollectionClassBuilderException("routeConfigFile ".$this->routeConfigFile." could not be found", 500);
            }
        } catch (Throwable $exception) {
            throw new RouteCollectionClassBuilderException($exception->getMessage(), 500, $exception);
        }
    }

    /**
     * @param Route $parentRoute
     * @return RouteCollection
     */
    public function getClassMethodRoutes(Route $parentRoute) : RouteCollection {
        return $this->routeMethodBuilder->getRoutes($parentRoute);
    }
}