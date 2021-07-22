<?php
namespace singleframe\Tests\Routing\Unit;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use singleframe\Http\Request\HttpRequest;
use singleframe\Http\Response\HttpResponse;
use singleframe\Http\Response\IHttpResponse;
use singleframe\Injector\Injector;
use singleframe\Log\ILogger;
use singleframe\Log\Logger;
use singleframe\Routing\IRouteClassMethodBuilder;
use singleframe\Routing\Route;
use singleframe\Routing\RouteBuilder\RouteClassBuilder;
use singleframe\Routing\RouteBuilder\RouteClassMethodAnnotationBuilder;
use singleframe\Routing\RouteCollection;
use singleframe\Routing\Router;

class RouterTest extends TestCase {

    private function get_routes_config() : array {
        return [
            new Route("/ad/{id}", RouterTestClassController::class),
            new Route("/target", RouterTestClassController2::class),
        ];
    }

    function testRouterDirectly() {
        $request                                    = new HttpRequest("GET", "https://www.google.com/ad/131212/targets");
        $request->withQueryParams(["question" => "yes"]);
        //
        $router                                     = new Router(
            new RouteClassBuilder(
                new RouteCollection(...array_values($this->get_routes_config())),
                new RouteClassMethodAnnotationBuilder()
            ),
            new Injector("", new Logger()),
            new Logger()
        );
        $router->process($request);
        $this->assertTrue(true);
    }

    private function get_di_config() : array {
        return [
            RouteCollection::class                  => $this->get_routes_config(),
            IRouteClassMethodBuilder::class         => RouteClassMethodAnnotationBuilder::class,
            ILogger::class                          => Logger::class
        ];
    }

    /*function testRouterWithInjector() {
        $request                                    = new HttpRequest("GET", "https://www.google.com/ad/131212/targets");
        $request->withQueryParams(["question" => "yes"]);

        $router                                     = (new Injector(
            $this->get_di_config()
        ))->get(Router::class);
        $router->process($request);
        $this->assertTrue(true);
    }*/
}
class RouterTestResponseObject implements JsonSerializable {
    public int $publicInt = 12;
    protected int $protectedInt = 14;
    public function jsonSerialize() {
        //return get_object_vars($this);
        return $this;
    }
}
class RouterTestClassController {
    /**
     * @Route:uri /targets
     * @Route:method get
     */
    public function getAd(int $id, string $question) : IHttpResponse {
        return new HttpResponse(200);
    }

    /**
     * @Route:uri /
     * @Route:method get
     */
    public function getEmpty() : IHttpResponse {
        return (new HttpResponse)::createEmptyResponse(200);
        //return (new HttpResponse)::createJsonResponse(200, ["publicInt" => 12]);
        //return (new HttpResponse)::createJsonResponse(200, new RouterTestResponseObject());
        //return (new HttpResponse)::createJsonResponse(200, null);
    }

    /**
     * @Route:uri /
     * @Route:method post
     */
    public function postAd() : IHttpResponse {
        return new HttpResponse(200);
    }

    /**
     * @Route:uri /
     * @Route:method put
     */
    public function putAd() : IHttpResponse {
        return new HttpResponse(201);
    }
}
class RouterTestClassController2 {}