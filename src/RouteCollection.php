<?php
namespace Terrazza\Component\Routing;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class RouteCollection implements IteratorAggregate, Countable {
    /**
     * @var Route[]
     */
    private array $routes = [];

    public function __construct(Route ...$routes) {
        $this->routes = $routes;
    }

    public function add(Route $route) : self {
        $this->routes[] = $route;
        return $this;
    }

    public function getIterator() : ArrayIterator {
        return new ArrayIterator($this->routes);
    }

    public function count() : int {
        return count($this->routes);
    }
}