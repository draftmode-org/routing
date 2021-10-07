<?php

namespace Terrazza\Component\Routing;

class RouteSearchClass {
    /**
     * @var string
     */
    private string $searchUri;
    /**
     * @var string
     */
    private string $searchMethod;

    /**
     * @param string $searchUri
     * @param string $searchMethod
     */
    public function __construct(string $searchUri, string $searchMethod="GET")
    {
        $this->searchUri = $searchUri;
        $this->searchMethod = $searchMethod;
    }

    /**
     * @return string
     */
    public function getSearchUri(): string
    {
        return $this->searchUri;
    }

    /**
     * @return string
     */
    public function getSearchMethod(): string
    {
        return $this->searchMethod;
    }

}