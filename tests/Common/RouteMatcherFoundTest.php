<?php
namespace Terrazza\Component\Routing\Tests\Common;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\RouteMatcherFound;

class RouteMatcherFoundTest extends TestCase {
    function testNativeMethods() {
        $class = new RouteMatcherFound($uri="myUri", $className = "name", $classMethod = "method");
        $this->assertEquals([
            $uri,
            $className,
            $classMethod
        ],[
            $class->getUri(),
            $class->getClassName(),
            $class->getClassMethod()
        ]);
    }
}