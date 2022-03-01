<?php

namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteMatcher;
use Terrazza\Component\Routing\RouteSearch;
use Terrazza\Component\Routing\Tests\_Mocks\LoggerMock;

class RouteMatcherGetRouteTest extends TestCase {
    private function getRouteMatcher(bool $log=false) {
        return new RouteMatcher(LoggerMock::get($log));
    }

    function testMatcher() {
        $routes     = [
            new Route("payment", RouteMatcherGetRouteTestController::class)
        ];
        $this->assertEquals([
            null,                   // classRoute not found
            null,                   // classMethod not found
            null,                   // classMethod method not found

            "methodList",
            "methodList",
            "methodPost",
            "methodPost",
            "methodView",
            "methodView",
            "methodPut",
            "methodPut",
        ], [
            ($this->getRouteMatcher())->getRoute(new RouteSearch("unknown"), $routes),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/1234/12"), $routes),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment", "DELETE"), $routes),

            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment", "POST"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/", "POST"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/1234"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/1234/"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/1234", "PUT"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/1234/", "PUT"), $routes)->getClassMethodName(),
        ]);
    }

    function testUnknownClass() {
        $routes     = [
            new Route("payment", "unknownClass")
        ];
        $this->assertNull($this->getRouteMatcher()->getRoute(new RouteSearch("payment"), $routes));
    }

    function testNoMethodsInClass() {
        $routes     = [
            new Route("payment", RouteMatcherGetRouteTestControllerNoMethods::class)
        ];
        $this->assertNull($this->getRouteMatcher()->getRoute(new RouteSearch("payment"), $routes));
    }

    function xtestBestMethodMatch() {
        $routes     = [
            new Route("payment", RouteMatcherGetRouteTestControllerPayment::class),
            new Route("payment/view", RouteMatcherGetRouteTestControllerPaymentView::class),
        ];
        $this->assertEquals([
            "methodView1",
            "paymentView",
            "methodView3",
            "methodView2",
        ], [
            ($this->getRouteMatcher(true))->getRoute(new RouteSearch("payment/view1"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/view/1"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/view3"), $routes)->getClassMethodName(),
            ($this->getRouteMatcher())->getRoute(new RouteSearch("payment/view4"), $routes)->getClassMethodName(),
        ]);
    }
}

class RouteMatcherGetRouteTestController {
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

class RouteMatcherGetRouteTestControllerNoMethods {
    /**
     * @return string
     */
    function methodView() : string {
        return "methodView";
    }
}

class RouteMatcherGetRouteTestControllerPayment {
    /**
     * @Route/method GET
     * @Route/uri /view1
     * @return string
     */
    function methodView1() : string {
        return "methodView1";
    }

    /**
     * @Route/method GET
     * @Route/uri /{id}
     * @return string
     */
    function methodView2() : string {
        return "methodView2";
    }

    /**
     * @Route/method GET
     * @Route/uri /view3
     * @return string
     */
    function methodView3() : string {
        return "methodView3";
    }
}

class RouteMatcherGetRouteTestControllerPaymentView {

    /**
     * @Route/method GET
     * @Route/uri /{id}
     * @return string
     */
    function methodView() : string {
        return "paymentView";
    }
}