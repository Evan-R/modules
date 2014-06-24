#Selene routing component.

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
