<?php
namespace Terrazza\Component\Routing;

class RouteFound {
    private Route $route;
    private int $staticUriLen;

    public function __construct(Route $route, int $staticUriLen) {
        $this->route                                = $route;
        $this->staticUriLen                         = $staticUriLen;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * @return int
     */
    public function getStaticUriLen(): int
    {
        return $this->staticUriLen;
    }

}