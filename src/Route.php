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
     * @return array|null
     */
    public function getMethod(): ?array {
        return $this->methods;
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
}