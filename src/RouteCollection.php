<?php
namespace Terrazza\Component\Routing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class RouteCollection implements RouteCollectionInterface, IteratorAggregate, Countable {
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function __construct(Route ...$routes) {
        $this->routes                               = $routes;
    }

    /**
     * @param Route $route
     * @return RouteCollectionInterface
     */
    public function add(Route $route) : RouteCollectionInterface {
        $this->routes[]                             = $route;
        return $this;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator() : ArrayIterator {
        return new ArrayIterator($this->routes);
    }

    /**
     * @return int
     */
    public function count() : int {
        return count($this->routes);
    }
}