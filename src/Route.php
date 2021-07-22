<?php
namespace singleframe\Routing;

class Route {
    private string $routeUri;
    private string $routeClassName;
    private ?array $methods;

    public function __construct(string $routeUri, string $routeClassName, array $methods=null) {
        $this->routeUri                             = $routeUri;
        $this->routeClassName                       = $routeClassName;
        $this->methods                              = $methods;
    }

    function hasMethod(string $method) : bool {
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

    public function getRouteUri() : string {
        return $this->routeUri;
    }

    public function getRouteClassName() : string {
        return $this->routeClassName;
    }

    protected function cleanUri(string $uri) : string {
        if (substr($uri, -1) === "/") {
            return substr($uri, 0, -1);
        }
        return $uri;
    }

    function hasRoute(string $uri) :?RouteMatch {
        $uri                                        = $this->cleanUri($uri);
        $routePath                                  = $this->cleanUri($this->routeUri);
        $preMatchPosition                           = 0;
        if (preg_match_all('#\{([\w\_]+)\}#', $routePath, $matches, PREG_OFFSET_CAPTURE)) {
            $preMatchPosition                       = $matches[1][0][1];
        }
        if (strlen($routePath) === 0 && strlen($uri) === 0) {
            return new RouteMatch($this, 0);
        }
        $rRoutePath                                 = $routePath;
        $routePath                                  = '^' . preg_replace('#\{[\w\_]+\}#', '(.+?)', $routePath) . '$';
        if (preg_match("#".$routePath."#", "/" . trim($uri, "/"), $matches)) {
            return new RouteMatch($this, $preMatchPosition);
        } else {
            if (strlen($rRoutePath) && strpos($uri, $rRoutePath) !== false) {
                return new RouteMatch($this, 0);
            }
            return null;
        }
    }
}