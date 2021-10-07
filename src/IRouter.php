<?php
namespace Terrazza\Component\Routing;

use Terrazza\Http\Request\IHttpRequest;
use Terrazza\Http\Response\IHttpResponse;

interface IRouter {
    public function process(IHttpRequest $request) : IHttpResponse;
}