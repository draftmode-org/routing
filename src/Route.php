<?php
namespace Terrazza\Component\Routing;

class Route {
    /**
     * @var string
     */
    private string $uri;
    /**
     * @var array|null
     */
    private ?array $methods;
    /**
     * @var string
     */
    private string $className;

    /**
     * @var string|null
     */
    private ?string $classMethodName=null;

    /**
     * @var array
     */
    private array $arguments;

    public function __construct(string $uri, string $className, ?array $methods=null, ?array $arguments=null) {
        $this->uri                                  = $uri;
        $this->className                            = $className;
        $this->methods                              = $methods ?? [];
        $this->arguments                            = $arguments ?? [];
    }

    /**
     * @return string
     */
    public function getUri() : string {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getClassName() : string {
        return $this->className;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function hasMethod(string $method) : bool {
        if (count($this->methods)) {
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
     * @return array
     */
    public function getMethods(): array {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param string $uri
     * @param string $classMethodName
     * @param array|null $methods
     * @return $this
     */
    public function withMethodFilter(string $uri, string $classMethodName, ?array $methods=null) : self {
        $route                                      = clone $this;
        $uri                                        = trim($uri, "/");
        if ($uri) {
            $route->uri                             .= "/".$uri;
        }
        $route->methods                             = $methods ?? [];
        $route->classMethodName                     = $classMethodName;
        return $route;
    }

    /**
     * @return string|null
     */
    public function getClassMethodName() :?string {
        return $this->classMethodName;
    }
}