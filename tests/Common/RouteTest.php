<?php
namespace Terrazza\Component\Routing\Tests\Common;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;

class RouteTest extends TestCase {

    function testSimple() {
        $route = new Route(
            $uri = "route", $className = "className", ["GET"], $arguments = ["a" => "b"]
        );
        $this->assertEquals([
            true,
            false,
            $uri,
            $className,
            $arguments,
            null
        ],[
            $route->hasMethod("GET"),
            $route->hasMethod("POST"),
            $route->getUri(),
            $route->getClassName(),
            $route->getArguments(),
            $route->getClassMethodName()
        ]);
    }

    function testRouteOptionals() {
        $route = new Route(
            "/route/", "routeClassName", ["GET"]
        );
        $this->assertEquals([
            true,
            false,

            []
        ],[
            $route->hasMethod("GET"),
            $route->hasMethod("POST"),
            
            $route->getArguments()
        ]);
    }

    function testRouteWithMethodFilter() {
        $route = new Route(
            $uri = "route", $className = "className"
        );
        $methodRoute = $route->withMethodFilter($uri2 = "yes", $methodName = "methodName", $methods = ["GET"]);
        $this->assertEquals([
            $uri,
            $className,

            $uri . "/" . $uri2,
            $className,
            $methodName,
            $methods
        ], [
            $route->getUri(),
            $route->getClassName(),

            $methodRoute->getUri(),
            $methodRoute->getClassName(),
            $methodRoute->getClassMethodName(),
            $methodRoute->getMethods()
        ]);
    }
}