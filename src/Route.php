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
     * @var array
     */
    private array $arguments;

    public function __construct(string $uri, ?array $methods=null, ?array $arguments=null) {
        $this->uri                                  = $uri;
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
}