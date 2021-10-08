<?php

namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteMatcher;
use Terrazza\Component\Routing\RouteSearchClass;

class RouteMatcherGetRouteTest extends TestCase {

    function testNotFound() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("xy"), [
            new Route("/tests", RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            null,
            $route
        );
    }

    function testFoundNoParams1() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests"), [
            new Route("/tests", $found = RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFoundNoParams2() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests"), [
            new Route("/tests/after", RouteMatcherGetRouteTestController2::class),
            new Route("/tests", $found = RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFoundNoParams3() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests/after"), [
            new Route("/tests", RouteMatcherGetRouteTestController1::class),
            new Route("/tests/after", $found = RouteMatcherGetRouteTestController2::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFoundWithParams1() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests/12131"), [
            new Route("/tests/{id}", $found = RouteMatcherGetRouteTestController1::class),
            new Route("/tests/after/{id}", RouteMatcherGetRouteTestController2::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFoundWithParams2() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests/12131"), [
            new Route("/tests/after/{id}", RouteMatcherGetRouteTestController2::class),
            new Route("/tests/{id}", $found = RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFoundWithParams3() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests/method/save"), [
            new Route("/tests/method/{id}", $found = RouteMatcherGetRouteTestController2::class),
            new Route("/tests/{id}/save", RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFoundWithParams4() {
        $route = (new RouteMatcher())->getRoute(
            new RouteSearchClass("/tests/method/save"), [
            new Route("/tests/{id}/save", RouteMatcherGetRouteTestController1::class),
            new Route("/tests/method/{id}", $found = RouteMatcherGetRouteTestController2::class),
            new Route("/tests/{id}/delete", RouteMatcherGetRouteTestController3::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function testFindWithMethod() {
        $routes                 = [
            new Route("/tests", RouteMatcherGetRouteTestController1::class),
        ];
        $foundList            = (new RouteMatcher())->getRoute(new RouteSearchClass("/tests/get"),$routes, true);
        $foundView            = (new RouteMatcher())->getRoute(new RouteSearchClass("/tests/get/1212"),$routes, true);
        $foundPost            = (new RouteMatcher())->getRoute(new RouteSearchClass("/tests/get", "POST"),$routes, true);
        $this->assertEquals([
            RouteMatcherGetRouteTestController1::class,
            "methodList",

            RouteMatcherGetRouteTestController1::class,
            "methodView",

            RouteMatcherGetRouteTestController1::class,
            "methodPost"
        ],[
            $foundList->getClassName(),
            $foundList->getClassMethod(),

            $foundView->getClassName(),
            $foundView->getClassMethod(),

            $foundPost->getClassName(),
            $foundPost->getClassMethod()
        ]);
    }

    function testNotFoundWithMethod() {
        $routes                 = [
            new Route("/tests", RouteMatcherGetRouteTestController1::class),
        ];
        $notFound             = (new RouteMatcher())->getRoute(new RouteSearchClass("/tests/not"),$routes, true);
        $this->assertEquals([
            null
        ],[
            $notFound
        ]);
    }

    function testNotFoundNoMethod() {
        $routes                 = [
            new Route("/tests", RouteMatcherGetRouteTestController2::class),
        ];
        $notFound             = (new RouteMatcher())->getRoute(new RouteSearchClass("/tests"),$routes, true);
        $this->assertEquals([
            null
        ],[
            $notFound
        ]);
    }

    function testExceptionClass() {
        $this->expectException(RuntimeException::class);
        (new RouteMatcher())->getRoute(new RouteSearchClass("/tests/not"),[
            new Route("/tests", "unknownClass")
        ], true);
    }
}

class RouteMatcherGetRouteTestController1 {
    /**
     * @Route/method GET
     * @Route/uri /get
     * @return string
     */
    function methodList() : string {
        return "methodLIst";
    }
    /**
     * @Route/method GET
     * @Route/uri /get/{id}
     * @return string
     */
    function methodView() : string {
        return "methodView";
    }
    /**
     * @Route/method POST
     * @Route/uri /get
     * @return string
     */
    function methodPost() : string {
        return "methodPost";
    }
}
class RouteMatcherGetRouteTestController2 {
    /**
     * @param string $data
     */
    function methodList(string $data) : void {}
}
class RouteMatcherGetRouteTestController3 {}