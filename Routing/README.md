#Selene routing component.

## The Router


```php
<?php

use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\Router;
use \Selene\Components\Routing\Events\RouterEvents as Events;
use \Selene\Components\Routing\RouteCollection as Routes;
use \Selene\Components\Routing\Controller\Dispatcher as Controllers;
use \Selene\Components\Events\Dispatcher;

$events = new Dispatcher;

// add some event listeners
$events->on(Events::FILTER_BEFORE.'.my_filter', function($event) {…});
$events->on(Events::DISPATCHED, function() {…});
$events->on(Events::FILTER_AFTER.'.my_filter', function($event) {…});

$routes = new Routes;

// add some routes
$routes->add(new Route(…));

$router = new Router(
    new Controllers,
    $routes,
    $events
);


try {
    $router->dispatch($request);
} catch (\Selene\Components\Routing\Exception\RouteNotFoundException $e) {
    …    
}
```

### Router Events

When routing a request, the router will emmit different events.

__RouterEvents::FILTER_BEFORE__

is a prefix to before the filter name, seperated by a dot (.), e.g.
`RouterEvents::FILTER_BEFORE.'.my_filter'.`

The event is fired before the route is being dipatched.

__RouterEvents::FILTER_AFTER__
is a prefix to before the filter name, seperated by a dot (.), e.g.
`RouterEvents::FILTER_AFTER.'.my_filter'.`

The event is fired after the route has been dispatched.

__RouterEvents::DISPATCHED__

This event is fired after the route was dispatched (the controller has been
successfully called).

__RouterEvents::NOT_FOUND__

This event is fired just before the `RouteNotFoundException` is thrown.

## Routes

The route builder class allows for easy creation of a routes collection.

### Building routes

```php

<?php

use \Selene\Components\Routing\RouteBuilder;

$builder = new RouteBuilder;

$builder->define('GET', 'index', '/', 'Controller:indexAction');

```
This can also be written as: 

```php

<?php

$builder->get('index', '/', 'Controller:indexAction');

```

There're shortcut methods for each http verb, `get()`,`post()`, `put()`, `delete()`, also an `any()` method.

If you need to pass more than one http verb to a route, use define and write
your verbs as follows

```php
$builder->define('GET|POST', 'index', '/', 'Controller:indexAction');

```

### Building Resources
```php

<?php

$builder->resource('photo/', 'Photo');

$builder->resource('photo/', 'Photo', ['GET', 'POST', 'DELETE']);

```


## Loading routes with the DI container


```php
<?php

use \Selene\Components\Routing\Loader\XmlLoader;

$loader = new XmlLoader($ConatinerBuilder, $locator);

$loader->load('routes.xml');

```
__routing.xml__

```xml

<?xml version="1.0" encoding="UTF-8"?>

<routes>
  <get name="index" path="/" action="IndexController:indexAction"/>
</routes>

```
