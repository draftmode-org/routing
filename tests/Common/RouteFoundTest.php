<?php
namespace Terrazza\Component\Routing\Tests\Common;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteFound;

class RouteFoundTest extends TestCase {
    function testNativeMethods() {
        $class = new RouteFound($route = new Route(
            "uri",
            "routeClass"
        ), $staticUriLen = 10);
        $this->assertEquals([
            $route,
            $staticUriLen
        ],[
            $class->getRoute(),
            $class->getStaticUriLen()
        ]);
    }
}