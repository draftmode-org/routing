<?php
namespace Terrazza\Component\Routing;

class RouteMatcherFound {
    private string $uri;
    private string $className;
    private ?string $classMethod;

    public function __construct(string $uri, string $className, string $classMethod=null) {
        $this->uri                                  = $uri;
        $this->className                            = $className;
        $this->classMethod                          = $classMethod;
    }

    public function getUri() : string {
        return $this->uri;
    }
    /**
     * @return string
     */
    public function getClassName(): string {
        return $this->className;
    }

    /**
     * @return string|null
     */
    public function getClassMethod() :?string {
        return $this->classMethod;
    }

}