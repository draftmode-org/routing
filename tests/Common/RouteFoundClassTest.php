<?php
namespace Terrazza\Component\Routing\Tests\Common;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteFoundClass;

class RouteFoundClassTest extends TestCase {
    function testNativeMethods() {
        $class = new RouteFoundClass($route = new Route(
            "uri",
            "routeClass"
        ), $preMatchPosition = 10);
        $this->assertEquals([
            $route,
            $preMatchPosition
        ],[
            $class->getRoute(),
            $class->getPreMatchPosition()
        ]);
    }
}