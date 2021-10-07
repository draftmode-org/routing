<?php

namespace Terrazza\Component\Routing;

interface RouteCollectionInterface
{
    public function add(Route $route) : RouteCollectionInterface;
}