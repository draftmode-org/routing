<?php
namespace Terrazza\Component\Routing;

class Route implements RouteInterface {
    private string $routeUri;
    private string $routeClassName;
    private ?array $methods;

    public function __construct(string $routeUri, string $routeClassName, array $methods=null) {
        $this->routeUri                             = $routeUri;
        $this->routeClassName                       = $routeClassName;
        $this->methods                              = $methods;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function hasMethod(string $method) : bool {
        if ($this->methods) {
            if ($method === "HEAD") $method = "GET";
            foreach ($this->methods as $routeMethod) {
                if (strtolower($method) === strtolower($routeMethod)) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getRouteUri() : string {
        return $this->routeUri;
    }

    /**
     * @return string
     */
    public function getRouteClassName() : string {
        return $this->routeClassName;
    }

    /**
     * @param string $uri
     * @return RouteFoundClass|null
     */
    public function getMatchedRoute(string $uri) :?RouteFoundClass {
        $uri                                        = trim($uri, "/");
        $routePath                                  = trim($this->routeUri, "/");
        $preMatchPosition                           = 0;
        if (preg_match_all('#\{([\w\_]+)\}#', $routePath, $matches, PREG_OFFSET_CAPTURE)) {
            $preMatchPosition                       = $matches[1][0][1];
        }
        if (strlen($routePath) === 0 && strlen($uri) === 0) {
            return new RouteFoundClass($this, 0);
        }
        $rRoutePath                                 = $routePath;
        $routePath                                  = '^' . preg_replace('#\{[\w\_]+\}#', '(.+?)', $routePath) . '$';
        if (preg_match("#".$routePath."#", trim($uri, "/"), $matches)) {
            return new RouteFoundClass($this, $preMatchPosition);
        } else {
            if (strlen($rRoutePath) && strpos($uri, $rRoutePath) !== false) {
                return new RouteFoundClass($this, 0);
            }
            return null;
        }
    }
}