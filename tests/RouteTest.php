<?php
namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteFoundClass;

class RouteTest extends TestCase {

    function testSimpleRoute() {
        $route = new Route(
            $uri = "route", $routeClassName = "routeClassName", ["GET"]
        );
        $this->assertEquals([
            true,
            false,
            $uri,
            $routeClassName,
            new RouteFoundClass($route, 0),
            null,
        ],[
            $route->hasMethod("GET"),
            $route->hasMethod("POST"),
            $route->getRouteUri(),
            $route->getRouteClassName(),
            $route->getMatchedRoute($uri),
            $route->getMatchedRoute("/hallo"),
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

    function testRouteWithSlashes() {
        $route = new Route(
            $uri = "/route/", "routeClassName"
        );
        $this->assertEquals([
            new RouteFoundClass($route, 0),
            new RouteFoundClass($route, 0),
            null,
        ],[
            $route->getMatchedRoute($uri),
            $route->getMatchedRoute("route"),
            $route->getMatchedRoute("/hallo"),
        ]);
    }

    function testEmptyRoute() {
        $route = new Route(
            $uri = "", "routeClassName"
        );
        $this->assertEquals([
            new RouteFoundClass($route, 0),
            null,
            null,
        ],[
            $route->getMatchedRoute($uri),
            $route->getMatchedRoute("route"),
            $route->getMatchedRoute("/hallo"),
        ]);
    }

    function testRouteWithArgs() {
        $route = new Route(
            $uri = "/route/{id}", "routeClassName"
        );
        $this->assertEquals([
            new RouteFoundClass($route, strpos($uri, "{")),
        ],[
            $route->getMatchedRoute("/route/1234")
        ]);
    }

    function testRouteWithArgsInsideUri() {
        $route = new Route(
            $uri = "route/{id}/ad", "routeClassName"
        );
        $this->assertEquals([
            new RouteFoundClass($route, strpos($uri, "{")+1),
        ],[
            $route->getMatchedRoute("route/1234/ad")
        ]);
    }
}