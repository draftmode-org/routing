<?php
namespace Terrazza\Component\Routing;

class RouteMatch {
    private Route $route;
    private int $preMatchPosition;

    public function __construct(Route $route, int $preMatchPosition=0) {
        $this->route                                = $route;
        $this->preMatchPosition                     = $preMatchPosition;
    }

    /**
     * @return Route
     */
    public function getRoute(): Route {
        return $this->route;
    }

    /**
     * @return int
     */
    public function getPreMatchPosition(): int {
        return $this->preMatchPosition;
    }

}