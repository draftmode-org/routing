# the routing component
The component match routes.<br>
_The component does not include any class loading or injection._<br>
_The only goal is: **find/match routes**_

1. Object/Classes
    1. [Route](#object-route)
    1. [RouteSearch](#object-route-search)
    3. [RouteMatcher](#object-route-matcher)
2. [Install](#install)
3. [Requirements](#require)
4. [Examples](#examples) 

## Object/Classes
<a id="object-route" name="object-route"></a>
<a id="user-content-object-route" name="user-content-object-route"></a>
### Route
The class covers the "route" to a given Controller ($className).
Properties:
- uri (string, required)
- className (string, required)
- methods (array, optional)
- arguments (array, optional)
#### method: hasMethod(string $method) : bool
If the [Route](#object-route) includes methods (which is optional) hasMethod validate the give one.<br>
_notice:<br>
$method "HEAD" will be replaced with "GET"_

<a id="object-route-search" name="object-route-search"></a>
<a id="user-content-object-route-search" name="user-content-object-route-search"></a>
### RouteSearch
The class covers the base uri, method and arguments. All [Routes](#object-route) are matched against this object.<br>
Properties:
- uri (string, required)
- method (string, required, default=GET)
- arguments (array, optional)

<a id="object-route-matcher" name="object-route-matcher"></a>
<a id="user-content-object-route-matcher" name="user-content-object-route-matcher"></a>
### RouteMatcher
#### method: getRoute(RouteSearch $routeSearch, array $routes) :?Route
To get a route is done in two steps.
1. match Route->uri against routeSearch->uri + method + arguments
2. get all public methods within the annotation @Route/uri and match against routeSearch + method + arguments
<br>**The annotation URI is without the Controller Route Uri and is required**


<a id="install" name="install"></a>
<a id="user-content-install" name="user-content-install"></a>
## How to install
### Install via composer
```
composer require terrazza/routing
```
<a id="require" name="require"></a>
<a id="user-content-require" name="user-content-require"></a>
## Requirements
### php version
- \>= 7.4
### composer packages
- psr/log

<a id="examples" name="examples"/></a>
<a id="user-content-examples" name="user-content-examples"/></a>
## Examples
**notice: The example requires a Psr\Log\LoggerInterface implementation**<br>

```

use Terrazza\Component\Routing\Route;
use Terrazza\Component\Routing\RouteMatcher;
use Terrazza\Component\Routing\RouteSearch;

class ControllerPayment {
    /**
     * @Route/method GET
     * @Route/uri /view1
     * @return string
     */
    function methodView1() : string {
        return "methodView1";
    }

    /**
     * @Route/method GET
     * @Route/uri /{id}
     * @return string
     */
    function methodById() : string {
        return "methodById";
    }

    /**
     * @Route/method GET
     * @Route/uri /view3
     * @return string
     */
    function methodView3() : string {
        return "methodView3";
    }
}

class ControllerPaymentView {
    /**
     * @Route/method GET
     * @Route/uri /{id}
     * @return string
     */
    function paymentView() : string {
        return "paymentView";
    }
}

$routes     = [
    new Route("payment", ControllerPayment::class),
    new Route("payment/view", ControllerPaymentView::class),
];

//
// Psr\Log\LoggerInterface implementation
//
$logger = "IMPORTANT ! has to initialized";

echo (new RouteMatcher($logger))
    ->getRoute(new RouteSearch("payment/view1"), $routes)->getClassMethodName();
// found in ControllerPayment, method methodView1

echo (new RouteMatcher($logger))
    ->getRoute(new RouteSearch("payment/view/1"), $routes)->getClassMethodName();
// found ControllerPayment, method methodView2 but will be skipped cause $id includes /
// found in ControllerPaymentView, method paymentView

echo (new RouteMatcher($logger))
    ->getRoute(new RouteSearch("payment/view3"), $routes)->getClassMethodName(),
// found in ControllerPayment, method methodView3  

echo (new RouteMatcher($logger))
    ->getRoute(new RouteSearch("payment/view4"), $routes)->getClassMethodName(),
// found in ControllerPayment, method methodById      

```