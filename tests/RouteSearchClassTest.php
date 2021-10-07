<?php
namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteFoundClass;
use Terrazza\Component\Routing\RouteSearchClass;

class RouteSearchClassTest extends TestCase {
    function testNativeMethods() {
        $class = new RouteSearchClass(
            $searchUri = "uri",
            $searchMethod = "GET");
        $this->assertEquals([
            $searchUri,
            $searchMethod
        ],[
            $class->getSearchUri(),
            $class->getSearchMethod()
        ]);
    }
}