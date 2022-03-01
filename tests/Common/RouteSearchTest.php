<?php
namespace Terrazza\Component\Routing\Tests\Common;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteFound;
use Terrazza\Component\Routing\RouteSearch;

class RouteSearchTest extends TestCase {
    function testNativeMethods() {
        $class = new RouteSearch(
            $uri = "uri",
            $method = "GET", $arguments = ["a" => 12]);
        $this->assertEquals([
            $uri,
            $method,
            $arguments,
        ],[
            $class->getUri(),
            $class->getMethod(),
            $class->getArguments()
        ]);
    }
}