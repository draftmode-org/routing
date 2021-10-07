<?php
namespace Terrazza\Component\Routing;

interface IRouteClassMethodBuilder {
    /**
     * @param Route $parentRoute
     * @return RouteCollection
     */
    public function getRoutes(Route $parentRoute): RouteCollection;
}