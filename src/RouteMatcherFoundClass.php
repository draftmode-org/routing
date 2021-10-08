<?php
namespace Terrazza\Component\Routing;

class RouteMatcherFoundClass {
    private string $className;
    private ?string $classMethod;

    public function __construct(string $className, string $classMethod=null) {
        $this->className                            = $className;
        $this->classMethod                          = $classMethod;
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