<?php
namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\RouteMatcherFoundClass;

class RouteMatcherFoundClassTest extends TestCase {
    function testNativeMethods() {
        $class = new RouteMatcherFoundClass($className = "name", $classMethod = "method");
        $this->assertEquals([
            $className,
            $classMethod
        ],[
            $class->getClassName(),
            $class->getClassMethod()
        ]);
    }
}