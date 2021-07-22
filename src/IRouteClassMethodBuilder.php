<?php
namespace singleframe\Routing;

interface IRouteClassMethodBuilder {
    /**
     * @param Route $parentRoute
     * @return RouteCollection
     */
    public function getRoutes(Route $parentRoute): RouteCollection;
}