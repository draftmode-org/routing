<?php
namespace singleframe\Tests\Routing\Unit;

use PHPUnit\Framework\TestCase;
use singleframe\Http\Request\HttpRequest;
use singleframe\Routing\Matcher\UriMatcher;
use singleframe\Routing\Route;
use singleframe\Routing\RouteCollection;

class RouteBestMatchTest extends TestCase {
    function testRoutingCollectionNoParams1() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests", $found = RouteClassBestMatchTestClassController1::class),
            new Route("/tests/after", RouteClassBestMatchTestClassController2::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

    function testRoutingCollectionNoParams2() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests/after", RouteClassBestMatchTestClassController2::class),
            new Route("/tests", $found = RouteClassBestMatchTestClassController1::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

    function testRoutingCollectionNoParams3() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests", RouteClassBestMatchTestClassController1::class),
            new Route("/tests/after", $found = RouteClassBestMatchTestClassController2::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/after");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

    function testRoutingCollectionWithParams1() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests/{id}", $found = RouteClassBestMatchTestClassController1::class),
            new Route("/tests/after/{id}", RouteClassBestMatchTestClassController2::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/12131");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

    function testRoutingCollectionWithParams2() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests/after/{id}", RouteClassBestMatchTestClassController2::class),
            new Route("/tests/{id}", $found = RouteClassBestMatchTestClassController1::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/12131");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

    function testRoutingCollectionWithParams3() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests/method/{id}", $found = RouteClassBestMatchTestClassController2::class),
            new Route("/tests/{id}/save", RouteClassBestMatchTestClassController1::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/method/save");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

    function testRoutingCollectionWithParams4() {
        $matcher        = new UriMatcher(new RouteCollection(
            new Route("/tests/{id}/save", RouteClassBestMatchTestClassController1::class),
            new Route("/tests/method/{id}", $found = RouteClassBestMatchTestClassController2::class),
            new Route("/tests/{id}/delete", RouteClassBestMatchTestClassController3::class),
        ));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/method/save");
        $this->assertEquals($found, $matcher->match($request)->getRouteClassName());
    }

}
class RouteClassBestMatchTestClassController1 {}
class RouteClassBestMatchTestClassController2 {}
class RouteClassBestMatchTestClassController3 {}