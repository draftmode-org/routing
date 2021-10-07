<?php
namespace Terrazza\Component\Routing\RouteBuilder;
use Terrazza\Component\Routing\IRouteClassMethodBuilder;
use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteCollection;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

class RouteClassMethodAnnotationBuilder implements IRouteClassMethodBuilder {
    protected static string $annotation = "Route";
    protected static string $regex = '/@%s(?:[ \t]*(.*?))?[ \t]*(?:\*\/)?\r?$/m';

    /**
     * @param Route $parentRoute
     * @return RouteCollection
     */
    public function getRoutes(Route $parentRoute): RouteCollection {
        $parentRouteUri                             = $parentRoute->getRouteUri();
        $parentClassName                            = $parentRoute->getRouteClassName();
        $reflectionClass                            = $this->get($parentClassName);
        $methods                                    = $this->getMethods($reflectionClass);
        $routes                                     = new RouteCollection();
        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $routes->add($this->getRouteFromMethod($parentRouteUri, $method));
        }
        return $routes;
    }

    /**
     * @param string $className
     * @return ReflectionClass
     */
    private function get(string $className) : ReflectionClass {
        try {
            return new ReflectionClass($className);
        } catch (ReflectionException $exception) {
            throw new RuntimeException("class ".$className." could not be loaded", $exception->getCode(), $exception);
        }
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return array
     */
    private function getMethods(ReflectionClass $reflectionClass) : array {
        $methods                                    = [];
        foreach ($reflectionClass->getMethods() as $method) {
            if (!$method->isPublic()) continue;
            $docBlock                               = $method->getDocComment();
            if (preg_match(sprintf(self::$regex, preg_quote(self::$annotation, "/")), $docBlock)) {
                $methods[]                          = $method;
            }
        }
        return $methods;
    }

    /**
     * @param string $parentUri
     * @param ReflectionMethod $reflectionMethod
     * @return Route
     */
    private function getRouteFromMethod(string $parentUri, ReflectionMethod $reflectionMethod) : Route {
        $docBlock                                   = $reflectionMethod->getDocComment();
        $uri                                        = null;
        $method                                     = $reflectionMethod->getName();
        $methods                                    = null;
        if (preg_match_all(sprintf(self::$regex, preg_quote(self::$annotation, "/")), $docBlock, $matches)) {
            foreach ($matches[1] as $match) {
                list($argument, $value)             = explode(" ", $match, 2);
                switch (trim($argument)) {
                    case ":uri":
                        $value                      = trim($value);
                        if ($value[0] === "/") {
                            $value                  = substr($value, 1);
                        }
                        $uri                        = $parentUri . "/" . $value;
                        break;
                    case ":method":
                        $methods                    = explode(",", trim($value));
                        break;
                }
            }
        }
        if (!$uri) {
            throw new RuntimeException(self::$annotation.":uri annotation for method ".$reflectionMethod->getName()." missing");
        }
        if (!$method) {
            throw new RuntimeException(self::$annotation.":method annotation for method ".$reflectionMethod->getName()." missing");
        }
        return new Route($uri, $method, $methods);
    }
}