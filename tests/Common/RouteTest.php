<?php
namespace Terrazza\Component\Routing\Tests\Common;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;

class RouteTest extends TestCase {

    function testSimpleRoute() {
        $route = new Route(
            $uri = "route", $routeClassName = "routeClassName", ["GET"]
        );
        $this->assertEquals([
            true,
            false,
            $uri,
            $routeClassName
        ],[
            $route->hasMethod("GET"),
            $route->hasMethod("POST"),
            $route->getRouteUri(),
            $route->getRouteClassName(),
        ]);
    }

    function testRouteWithoutMethod() {
        $route = new Route(
            $uri = "/route/", "routeClassName"
        );
        $this->assertEquals([
            true,
        ],[
            $route->hasMethod("GET")
        ]);
    }
}