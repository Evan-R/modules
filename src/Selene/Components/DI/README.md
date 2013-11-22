[![Build Status](https://api.travis-ci.org/seleneapp/dependency-injection.png?branch=development)](https://travis-ci.org/seleneapp/dependency-injection)

## Setup

```php
<?php

use Selene\Components\DI;

$container = new Container;
```

## Usage

---------

### Parameters

```php
<?php

$container->setParam('my.param', 'param value');
$container->setParam('my.options', [1, 2, 3]);

$container->getParam('my.param'); // 'param value'
$container->getParam('my.options'); // [1, 2, 3]
```                                               

---------

### Defining a service

```php
<?php
$container->setService('service_id', 'ServiceClass', ['service args']);
```

### Injecting a class instance as service

There're situations where it's not possible for a service being created by
the container. Therefor you may inject a class instance as a service. 

```php
<?php

$container->injectService('service_id', $instance);
```

---------

### Injection

#### Argument injection

```php
<?php

$container->setParam('my.param', 'param value');

// passing a parameter to the constructor of a service:
$container->setService('my_service', 'Acme\FooService', ['%my.param%']);
// or
$container->setService('my_service', 'Acme\FooService')
	->addArgument('%my.param%');

// passing a service reference to the constructor of a service:

$container->setService('other_service', 'Acme\OtherService');

$container->setService('my_service', 'Acme\ServiceNeedsOtherService', ['$other_service']);
// or
$container->setService('my_service', 'Acme\ServiceNeedsOtherService')
	->addArgument('$other_service');

```                                               

Notice the `$` symbol in front of the service id. `$` will indicate that you're
referencing a service. 

#### Setter injection

```php
<?php

$container->setParam('my.options', [1, 2, 3]);

// passing a parameter to a setter method of a service
$container->setService('my_service', 'Acme\FooService')
	->addSetter('setOptions', ['%my.options%'])

// passing a service reference to a setter method of a service

$container->setService('other_service', 'Acme\OtherService');

$container->setService('my_service', 'Acme\ServiceNeedsOtherService')
	->addSetter('setOtherService', ['%other_service%']);
```                                               
#### Factories

Sometimes you may find it easier to bootstrap a service using a factory. 
With regards to the IoC container, a factory is a static class method that takes
certain (or no) arguments, and returns an instance of your service.

```php
<?php

namespace Acme\Foo;

class ServiceFactory
{
	public static function makeFooService($fooargs = null)
	{
		return new FooService($fooargs);
	}
}

```

```php
<?php

$container->setParam('foo.options', ['opt1', 'opt2']);
$container->setParam('foo.factory', 'Acme\Foo\ServiceFactory');

$container
	->setService('foo_service')
	->setFactory('%foo.factory%', 'makeFooService')
	->addArgument('%foo.options%');

```
---------

### Scopes

Currently there're two different scopes, `container` and `prototype`. Services are
created as `container` by default. You may override this by setting the
respective scope. 

```php
<?php
$container->setService('my_service', 'Acme\FooService')
	->setScope(ContainerInterface::SCOPE_PROTOTYPE);
```

#### Services and scope container

Services defined with a `container` scope will return the same instance each time
the service is called.

#### Services and scope prototype

Unlike the container scope, setting the scope of a service to `prototype` will create a new instance of that
service each time it is called. 

---------

### Resolving a Service

All serices are lazy. A service gets resolved the first time it is called. 

```php
<?php
$service = $container->getService('my_service');
```

### Aliases

Serivices my also be aliased and resolved by their alias.

```php
<?php
$container->alias('my_service', 'my_alias');
```

### Service Inheritance

```php
<?php

$container->setParam('Acme\AbstractServiceClass', ['$foo_service']);
$container->setService('concrete_service', 'Acme\ConcreteServiceClass');

```

Now, all services that inherit directly from `Acme\AbstractServiceClass` will
get `foo_service` as the first constructor argument.

### Container as service dependecy

By default the container will set itself as `app.container` service, but you may
choose a different name if you have multiple containers. 

```php
<?php
$container = new Container(null, 'my.container');
```



