<?php

namespace Terrazza\Component\Routing;

class RouteSearch {
    /**
     * @var string
     */
    private string $uri;
    /**
     * @var string
     */
    private string $method;

    /**
     * @var array|null
     */
    private ?array $arguments;

    /**
     * @param string $uri
     * @param string $method
     * @param array|null $arguments
     */
    public function __construct(string $uri, string $method="GET", ?array $arguments=null){
        $this->uri = $uri;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array|null
     */
    public function getArguments(): ?array
    {
        return $this->arguments;
    }
}