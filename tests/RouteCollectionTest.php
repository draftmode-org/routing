<?php

namespace Terrazza\Component\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteCollection;

class RouteCollectionTest extends TestCase {
    function testNativeMethods() {
        $collection = new RouteCollection(
            $r1 = new Route("uri1", "routeClass1", ["GET"]),
            $r2 = new Route("uri1", "routeClass1", ["GET"]),
        );
        $collection->add(
            $r3 = new Route("uri1", "routeClass1", ["GET"])
        );
        $this->assertEquals([
            [$r1,$r2,$r3],
            3
        ],[
            iterator_to_array($collection->getIterator()),
            $collection->count()
        ]);

    }
}