<?php
namespace singleframe\Routing;

interface IHttpEntryPoint {
    public function process() : void;
}