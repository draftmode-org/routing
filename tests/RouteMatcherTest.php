<?php

namespace Terrazza\Component\Routing\Tests;
use PHPUnit\Framework\TestCase;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteCollection;
use Terrazza\Component\Routing\RouteMatcher;
use Terrazza\Component\Routing\RouteSearchClass;

class RouteMatcherTest extends TestCase {

    function testRoutingCollectionNotFound() {
        $collection         = new RouteCollection(
            new Route("/tests", RouteMatcherTestController1::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            null,
            $matcher->match(new RouteSearchClass("xy"))
        );
    }

    function testRoutingCollectionNoParams1() {
        $collection         = new RouteCollection(
            new Route("/tests", $found = RouteMatcherTestController1::class),
            new Route("/tests/after", RouteMatcherTestController2::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests"))->getRouteClassName()
        );
    }

    function testRoutingCollectionNoParams2() {
        $collection         = new RouteCollection(
            new Route("/tests/after", RouteMatcherTestController2::class),
            new Route("/tests", $found = RouteMatcherTestController1::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests"))->getRouteClassName()
        );
    }

    function testRoutingCollectionNoParams3() {
        $collection         = new RouteCollection(
            new Route("/tests", RouteMatcherTestController1::class),
            new Route("/tests/after", $found = RouteMatcherTestController2::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests/after"))->getRouteClassName()
        );
    }

    function testRoutingCollectionWithParams1() {
        $collection         = new RouteCollection(
            new Route("/tests/{id}", $found = RouteMatcherTestController1::class),
            new Route("/tests/after/{id}", RouteMatcherTestController2::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests/12131"))->getRouteClassName()
        );
    }

    function testRoutingCollectionWithParams2() {
        $collection         = new RouteCollection(
            new Route("/tests/after/{id}", RouteMatcherTestController2::class),
            new Route("/tests/{id}", $found = RouteMatcherTestController1::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests/12131"))->getRouteClassName()
        );
    }

    function testRoutingCollectionWithParams3() {
        $collection         = new RouteCollection(
            new Route("/tests/method/{id}", $found = RouteMatcherTestController2::class),
            new Route("/tests/{id}/save", RouteMatcherTestController1::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests/method/save"))->getRouteClassName()
        );
    }

    function testRoutingCollectionWithParams4() {
        $collection         = new RouteCollection(
            new Route("/tests/{id}/save", RouteMatcherTestController1::class),
            new Route("/tests/method/{id}", $found = RouteMatcherTestController2::class),
            new Route("/tests/{id}/delete", RouteMatcherTestController3::class),
        );
        $matcher            = new RouteMatcher($collection);
        $this->assertEquals(
            $found,
            $matcher->match(new RouteSearchClass("/tests/method/save"))->getRouteClassName()
        );
    }
}

class RouteMatcherTestController1 {}
class RouteMatcherTestController2 {}
class RouteMatcherTestController3 {}