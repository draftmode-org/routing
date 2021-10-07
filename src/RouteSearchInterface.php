<?php

namespace Terrazza\Component\Routing;

interface RouteSearchInterface {
    /**
     * @param mixed $request
     * @return RouteSearchClass
     */
    public static function getRouteSearch($request) : RouteSearchClass;
}