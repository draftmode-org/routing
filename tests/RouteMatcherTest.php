<?php

namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteMatcher;
use Terrazza\Component\Routing\RouteMatcherFound;
use Terrazza\Component\Routing\RouteSearchClass;
use Terrazza\Component\Routing\Tests\_Mocks\LoggerMock;

class RouteMatcherGetRouteTest extends TestCase {
    private function getRouteMatcher(bool $log=false) {
        return new RouteMatcher(LoggerMock::get($log));
    }

    function testClassNotFound() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("xy"), [
            new Route("/tests", RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertNull($route);
    }

    function testClassFoundMethodNotFound() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment", "DELETE"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertNull($route);
    }

    function testClassFoundMethodNotFoundSubFolder() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment/1234/12"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertNull($route);
    }

    function testClassFoundMethodFound_GET_methodList() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertEquals([
            RouteMatcherGetRouteTestController4::class,
            "methodList"
        ], [
            $route->getClassName(),
            $route->getClassMethod(),
        ]);
    }

    function testClassFoundMethodFound_GET_methodList2() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment/"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertEquals([
            RouteMatcherGetRouteTestController4::class,
            "methodList"
        ], [
            $route->getClassName(),
            $route->getClassMethod(),
        ]);
    }

    function testClassFoundMethodFound_GET_methodView() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment/1234"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertEquals([
            RouteMatcherGetRouteTestController4::class,
            "methodView"
        ], [
            $route->getClassName(),
            $route->getClassMethod(),
        ]);
    }

    function testClassFoundMethodFound_GET_methodView2() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment/1234/"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertEquals([
            RouteMatcherGetRouteTestController4::class,
            "methodView"
        ], [
            $route->getClassName(),
            $route->getClassMethod(),
        ]);
    }

    function testClassFoundMethodFound_GET_methodPut() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("payment/1234", "PUT"), [
            new Route("payment", RouteMatcherGetRouteTestController4::class)
        ]);
        $this->assertEquals([
            RouteMatcherGetRouteTestController4::class,
            "methodPut"
        ], [
            $route->getClassName(),
            $route->getClassMethod(),
        ]);
    }

    function xtestFoundNoParams1() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("/tests"), [
            new Route("/tests", $found = RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function xtestFoundNoParams2() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("/tests"), [
            new Route("/tests/after", RouteMatcherGetRouteTestController2::class),
            new Route("/tests", $found = RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function xtestFoundNoParams3() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("/tests/after"), [
            new Route("/tests", RouteMatcherGetRouteTestController1::class),
            new Route("/tests/after", $found = RouteMatcherGetRouteTestController2::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function xtestFoundWithParams1() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("/tests/12131"), [
            new Route("/tests/{id}", $found = RouteMatcherGetRouteTestController1::class),
            new Route("/tests/after/{id}", RouteMatcherGetRouteTestController2::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function xtestFoundWithParams2() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("/tests/12131"), [
            new Route("/tests/after/{id}", RouteMatcherGetRouteTestController2::class),
            new Route("/tests/{id}", $found = RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function xtestFoundWithParams3() {
        $route = ($this->getRouteMatcher())->getRoute(
            new RouteSearchClass("/tests/method/save"), [
            new Route("/tests/method/{id}", $found = RouteMatcherGetRouteTestController2::class),
            new Route("/tests/{id}/save", RouteMatcherGetRouteTestController1::class)
        ]);
        $this->assertEquals(
            $found,
            $route->getClassName()
        );
    }

    function xtestFoundWithParams4() {
        $route = ($this->getRouteMatcher())->getRoute(
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

    function xtestFindWithMethod() {
        $routes                 = [
            new Route("/tests", RouteMatcherGetRouteTestController1::class),
        ];
        $foundList            = ($this->getRouteMatcher())->getRoute(new RouteSearchClass("/tests/get"),$routes);
        $foundView            = ($this->getRouteMatcher())->getRoute(new RouteSearchClass("/tests/get/1212"),$routes);
        $foundPost            = ($this->getRouteMatcher())->getRoute(new RouteSearchClass("/tests/get", "POST"),$routes);
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

    function xtestNotFoundWithMethod() {
        $routes                 = [
            new Route("/tests", RouteMatcherGetRouteTestController1::class),
        ];
        $notFound             = ($this->getRouteMatcher())->getRoute(new RouteSearchClass("/tests/not"),$routes);
        $this->assertEquals([
            null
        ],[
            $notFound
        ]);
    }

    function xtestNotFoundNoMethod() {
        $routes                 = [
            new Route("/tests", RouteMatcherGetRouteTestController2::class),
        ];
        $notFound             = ($this->getRouteMatcher())->getRoute(new RouteSearchClass("/tests"),$routes);
        $this->assertEquals([
            null
        ],[
            $notFound
        ]);
    }

    function xtestExceptionClass() {
        $this->expectException(RuntimeException::class);
        ($this->getRouteMatcher())->getRoute(new RouteSearchClass("/tests/not"),[
            new Route("/tests", "unknownClass")
        ]);
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
     * @Route/method PUT
     * @Route/uri /get/{id}
     * @return string
     */
    function methodPut() : string {
        return "methodPut";
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
class RouteMatcherGetRouteTestController4 {
    /**
     * @Route/method GET
     * @Route/uri /
     * @return string
     */
    function methodList() : string {
        return "methodLIst";
    }
    /**
     * @Route/method GET
     * @Route/uri /{id}
     * @return string
     */
    function methodView() : string {
        return "methodView";
    }
    /**
     * @Route/method PUT
     * @Route/uri /{id}
     * @return string
     */
    function methodPut() : string {
        return "methodPut";
    }
    /**
     * @Route/method POST
     * @Route/uri /
     * @return string
     */
    function methodPost() : string {
        return "methodPost";
    }
}