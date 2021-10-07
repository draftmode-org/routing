<?php
namespace Terrazza\Component\Routing;

use Terrazza\Http\Request\IHttpRequest;
use Terrazza\Http\Response\HttpResponse;
use Terrazza\Http\Response\IHttpResponse;
use Terrazza\Injector\IInjector;
use Terrazza\Log\ILogger;
use Terrazza\Component\Routing\Exception\RouteClassMethodNotFoundException;
use Terrazza\Component\Routing\Exception\RouteClassNotFoundException;
use Terrazza\Component\Routing\Exception\RouteMissingParameterException;
use Terrazza\Component\Routing\Matcher\UriMatcher;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use RuntimeException;
use Throwable;

class Router implements IRouter {
    private IRouteClassBuilder $routesBuilder;
    private IInjector $injector;
    private ILogger $logger;
    private IHttpRequest $request;

    public function __construct(IRouteClassBuilder $routesBuilder,
                                IInjector $injector,
                                ILogger $logger) {
        $this->routesBuilder                        = $routesBuilder;
        $this->injector                             = $injector;
        $this->logger                               = $logger;
        $this->logger->startClass(self::class);
    }

    public function __destruct() {
        $this->logger->endClass();
    }

    public function process(IHttpRequest $request) : IHttpResponse {
        $this->request                              = $request;
        try {
            $matcher                                = new UriMatcher($this->routesBuilder->getClassRoutes());
            if ($route = $matcher->match($request)) {
                $routeMethodCollection              = $this->routesBuilder->getClassMethodRoutes($route);
                $matcher->setRoutes($routeMethodCollection);
                if ($routeMethod = $matcher->match($request)) {
                    $routeClassName                 = $route->getRouteClassName();
                    $routeMethodName                = $routeMethod->getRouteClassName();
                    $args                           = $this->getMethodArguments($routeMethod->getRouteUri(), $routeClassName, $routeMethodName);
                    //
                    $controller                     = $this->injector->get($routeClassName);
                    $method                         = (new ReflectionObject($controller))->getMethod($routeMethodName);
                    $result                         = $method->invokeArgs($controller, $args);
                } else {
                    throw new RouteClassMethodNotFoundException($route->getRouteClassName(), $matcher->searchUri($request));
                }
            } else {
                throw new RouteClassNotFoundException($matcher->searchUri($request));
            }
        } catch (Throwable $exception) {
            if ($exception->getCode() >= 400 && $exception->getCode() < 500) {
                $this->logger->warning(basename(get_class($exception))." ".$exception->getMessage());
                $result                             = new HttpResponse($exception->getCode());
            } else {
                $this->logger->exception($exception);
                $result                             = new HttpResponse(501);
            }
        }
        return $result;
    }

    private function getMethodArguments(string $routeUri, string $className, string $methodName) : array {
        $args                                       = [];
        try {
            $class                                  = new ReflectionClass($className);
        } catch (ReflectionException $exception) {
            throw new RuntimeException("class $className could not be loaded", $exception->getCode(), $exception);
        }
        try {
            $method = $class->getMethod($methodName);
        } catch (ReflectionException $exception) {
            throw new RuntimeException("method $methodName for class $className found", $exception->getCode(), $exception);
        }
        foreach ($method->getParameters() as $parameter) {
            $parameterName                          = $parameter->getName();
            if ($value = $this->request->getPathParam($routeUri, $parameterName)) {
                $args[$parameterName]               = $value;
            } elseif ($value = $this->request->getQueryParam($parameterName)) {
                $args[$parameterName]               = $value;
            } else {
                if ($parameter->isOptional()) {
                    $args[$parameterName]               = null;
                } else {
                    throw new RouteMissingParameterException("parameter $parameterName for $className::$methodName missing");
                }
            }
        }
        return $args;
    }
}