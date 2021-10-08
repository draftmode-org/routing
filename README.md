# the routing component
The class provides two methods to get a route from a route collection.
1. Route forwards to a Class
2. Routes are split into a mainController and methods with annotations inside
 
### Route forwards to a Class
```
$route = (new RouteMatcher())->getRoute(
    new RouteSearchClass("/tests/method/save"), [
        new Route("/tests/{id}/save", Controller1::class),
        new Route("/tests/method/{id}", Controller2::class),
        new Route("/tests/{id}/delete", Controller3::class)
    ]
);
echo $route->getClassName(); // Controller2::class

class Controller1 {}
class Controller2 {}
class Controller2 {}
```

### Routes are split into RouteMainClass and Methods inside
To split the routing into a mainController and methods inside is useful if you want to group 
methods for a given route. 

To get a valid method from a class a method annotation is required!<br>
@Route/uri {string}, is required<br>
<i>the uri is based on his parent class and is the extension of it</i>
@Route/method {string}, is optional<br>

The default prefix for that annotation /uri and /method is @Route.<br>
<i>can be modified within the __constructor of RouteMatcher</i><br><br>

```
$routes = [
    new Route("/payments", MainController1::class),
    new Route("/customers", MainController2::class),
];
// example1
$route = (new RouteMatcher())->getRoute(
    new RouteSearchClass("/payments"), // default method: GET
    $routes,
    true 
);
echo $route->getClassName();    // MainController1::class
echo $route->getClassMethod();  // methodList

// example2
$route = (new RouteMatcher())->getRoute(
    new RouteSearchClass("/payments/1212"), // default method: GET
    $routes,
    true 
);
echo $route->getClassName();    // MainController1::class
echo $route->getClassMethod();  // methodView

// example3
$route = (new RouteMatcher())->getRoute(
    new RouteSearchClass("/payments", "POST"),
    $routes,
    true 
);
echo $route->getClassName();    // MainController1::class
echo $route->getClassMethod();  // methodPost

class MainController1 {
    /**
     * @Route/method GET
     * @Route/uri /
     * @return string
     */
    function methodList() : string {
        return "methodLIst";
    }
    /**
     * @Route/method GET
     * @Route/uri /{id}
     * @return string
     */
    function methodView() : string {
        return "methodView";
    }
    /**
     * @Route/method POST
     * @Route/uri /
     * @return string
     */
    function methodPost() : string {
        return "methodPost";
    }
}

class MainController2 {
    /**
     * @param string $data
     */
    function methodList(string $data) : void {}
}

class MainController3 {
}
```