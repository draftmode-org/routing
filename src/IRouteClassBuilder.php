<?php
namespace Terrazza\Component\Routing;

interface IRouteClassBuilder {
    public function getClassRoutes(): RouteCollection;
    public function getClassMethodRoutes(Route $parentRoute) : RouteCollection;
    //
    public static function buildFromFile(string $routeConfigFile, IRouteClassMethodBuilder $routeMethodBuilder) : IRouteClassBuilder;
}