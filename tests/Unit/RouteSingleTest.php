<?php
namespace Terrazza\Tests\Routing\Unit;

use PHPUnit\Framework\TestCase;
use Terrazza\Http\Request\HttpRequest;
use Terrazza\Component\Routing\Matcher\UriMatcher;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteCollection;

class RouteSingleTest extends TestCase {

    function testRoutingWithoutMethod() {
        $route          = new Route("/tests", RouteClassSingleTestClassController::class);
        $matcher        = new UriMatcher(new RouteCollection($route));
        $request        = new HttpRequest("GET", "https://www.google.com/tests");
        $this->assertNotNull($matcher->match($request));
    }

    function testRoutingWithMethodFound() {
        $route          = new Route("/tests", RouteClassSingleTestClassController::class);
        $matcher        = new UriMatcher(new RouteCollection($route));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/");
        $this->assertNotNull($matcher->match($request));
    }

    function testRoutingWithMethodNotFound1() {
        $route          = new Route("/tests", RouteClassSingleTestClassController::class, ["POST"]);
        $matcher        = new UriMatcher(new RouteCollection($route));
        $request        = new HttpRequest("GET", "https://www.google.com/tests");
        $this->assertNull($matcher->match($request));
    }

    function testRoutingWithMethodNotFound2() {
        $route          = new Route("/tests", RouteClassSingleTestClassController::class, ["POST"]);
        $matcher        = new UriMatcher(new RouteCollection($route));
        $request        = new HttpRequest("GET", "https://www.google.com/mtests");
        $this->assertNull($matcher->match($request));
    }

    function testRoutingWithMethodNotFound3() {
        $route          = new Route("/mtests", RouteClassSingleTestClassController::class, ["POST"]);
        $matcher        = new UriMatcher(new RouteCollection($route));
        $request        = new HttpRequest("GET", "https://www.google.com/tests");
        $this->assertNull($matcher->match($request));
    }

    function testRoutingWithPatternsMethodFound() {
        $route          = new Route("/tests/{id}/", RouteClassSingleTestClassController::class);
        $matcher        = new UriMatcher(new RouteCollection($route));
        $request        = new HttpRequest("GET", "https://www.google.com/tests/12121");
        $this->assertNotNull($matcher->match($request));
    }
}

class RouteClassSingleTestClassController {}