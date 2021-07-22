<?php
namespace singleframe\Routing;

use singleframe\Http\Request\IHttpRequest;
use singleframe\Http\Response\IHttpResponse;

interface IRouter {
    public function process(IHttpRequest $request) : IHttpResponse;
}