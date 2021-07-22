<?php
declare(strict_types=1);
namespace singleframe\Routing;
use singleframe\Http\Message\IHttpMessageAdapter;
use singleframe\Http\Response\HttpResponse;
use singleframe\Injector\IInjector;
use Throwable;

class HttpEntryPoint implements IHttpEntryPoint {
    /**
     * @var IHttpMessageAdapter
     */
    private IHttpMessageAdapter $messageAdapter;

    /**
     * @var IInjector
     */
    private IInjector $injector;

    public function __construct(IHttpMessageAdapter $messageAdapter,
                                IInjector $injector,
                                IRouteClassBuilder $routeClassBuilder) {
        $this->messageAdapter                       = $messageAdapter;
        $this->injector                             = $injector;
        //
        $this->injector->push(IRouteClassBuilder::class, $routeClassBuilder);
        //
        //$this->injector->getLogger()->path()->push(self::class);
    }

    public function __destruct() {
        $this->injector->getLogger()->breadcrumb()->pop();
    }

    public function process() : void {
        try {
            $httpRequest                            = $this->messageAdapter::fromGlobals();
            /** @var IRouter $router */
            $router                                 = $this->injector->get(Router::class);
            $response                               = $router->process($httpRequest);
        } catch (Throwable $exception) {
            $this->injector->getLogger()->exception($exception);
            echo $exception->getTraceAsString();
            $response                               = new HttpResponse(501);
        }
        //
        // emit
        //
        if (headers_sent()) {
            $this->injector->getLogger()->error('headers were already sent. The response could not be emitted!');
        } else {
            $this->messageAdapter::emit($response, true);
        }
    }
}