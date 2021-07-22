<?php
namespace singleframe\Routing;

interface IRouteClassBuilder {
    public function getClassRoutes(): RouteCollection;
    public function getClassMethodRoutes(Route $parentRoute) : RouteCollection;
}