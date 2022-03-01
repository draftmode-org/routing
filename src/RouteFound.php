<?php
namespace Terrazza\Component\Routing;

class RouteFound {
    private Route $route;
    private int $position;

    public function __construct(Route $route, int $position) {
        $this->route                                = $route;
        $this->position                             = $position;
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
    public function getPosition(): int
    {
        return $this->position;
    }

}