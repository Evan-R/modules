[![Build Status](https://api.travis-ci.org/seleneapp/di.png?branch=development)](https://travis-ci.org/seleneapp/di)
[![HHVM Status](http://hhvm.h4cc.de/badge/selene/di.png)](http://hhvm.h4cc.de/package/selene/di)

[![Coverage Status](https://coveralls.io/repos/seleneapp/di/badge.png)](https://coveralls.io/r/seleneapp/di)

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

$container->setParameter('my.param', 'param value');
$container->setParameter('my.options', [1, 2, 3]);

$container->getParameter('my.param');   // 'param value'
$container->getParameter('my.options'); // [1, 2, 3]
```                                               

---------

### Defining a service

```php
<?php
$container->define('my.service', 'ServiceClass', ['service args']);
```

### Injecting a class instance as service

There're situations where it's not possible or not desired for a service being created by
the container. Therefor you may inject a class instance as a service. 

```php
<?php

$container
	->define('service_id', 'InjectedClass')
	->setInjected(true);

	// ...

$container->inject('service_id', $instance);
```

#### Syncing dependencies on injected services

If your service depends on a nother service that is injected, you cannot rely
on that dependency beeing declared on the service constructor. Instead you
should use setter injected as described below. Setters relying on injected
services are only called once all depencies are resolved. 

---------

### Injection

#### Constructor injection

```php
<?php

$container->setParameter('my.param', 'param value');

// passing a parameter to the constructor of a service:
$container->define('my_service', 'Acme\FooService', ['%my.param%']);
// or
$container->define('my_service', 'Acme\FooService')
	->addArgument('%my.param%');

// passing a service reference to the constructor of a service:

$container->define('other_service', 'Acme\OtherService');

$container->define('my_service', 'Acme\ServiceNeedsOtherService', ['$other_service']);
// or
$container->define('my_service', 'Acme\ServiceNeedsOtherService')
	->addArgument('$other_service');

```                                               

Notice the `$` symbol in front of the service id. `$` will indicate that you're
referencing a service. You may also create a reference object instead of using the
dollar notation. 

```php
<?php

use \Selene\Components\DI\Reference;

$container->define('my_service', 'Acme\ServiceNeedsOtherService')
	->addArgument(new Reference('other_service'));

```                                               


#### Setter injection

```php
<?php

$container->setParameter('my.options', [1, 2, 3]);

// passing a parameter to a setter method of a service
$container->define('my_service', 'Acme\FooService')
	->addSetter('setOptions', ['%my.options%'])

// passing a service reference to a setter method of a service

$container->define('other_service', 'Acme\OtherService');

$container->define('my_service', 'Acme\ServiceNeedsOtherService')
	->addSetter('setOtherService', ['%other_service%']);
```                                               
#### Factories

Sometimes you may find it easier to bootstrap a service using a factory. 
With regards to the DIC, a factory is a class method that takes
certain (or no) arguments, and returns an instance of your service.

The factory method can be both static or none static. All arguments declared on
the service definition will be injected to the factory method.

```php
<?php

namespace Acme\Foo;

class ServiceFactory
{
	public function makeFooService($fooargs = null)
	{
		return new FooService($fooargs);
	}
}

```

```php
<?php

$container->setParameter('foo.options', ['opt1', 'opt2']);
$container->setParameter('foo.factory.class', 'Acme\Foo\ServiceFactory');

$container
	->setService('foo_service')
	->setFactory('%foo.factory.class%', 'makeFooService')
	->addArgument('%foo.options%');

```
---------

### Scopes

Currently there're two different scopes, `container` and `prototype`. Services are
created as `container` by default. You may override this by setting the
respective scope. 

Note that you cannot change the scope of an injected servce
to `prototype`.

```php
<?php

$container->define('my_service', 'Acme\FooService')
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

All serices are lazy. A service is resolved the first time it is called. 

```php
<?php
$service = $container->get('my_service');
```

### Aliases

Serivices my also be aliased and resolved by their alias.

```php
<?php
$container->alias('my_service', 'my_alias');
```

### Service Inheritance

You may inherit dependencies from a parent service. The parent service can be
concrete or abstract.

Note that the childservice will inherit both constructor and setter injected
dependecies from its parent.

```php
<?php

$container->setParameter('Acme\AbstractServiceClass', ['$foo_service']);
$container
	->define('parent_service', 'Acme\ConcreteServiceClass')
	->setAbstract()
	->addArgument('foo');

$container
	->define('concrete_service', 'Acme\ConcreteServiceClass')
	->setParent(new Reference('parent_service'));

```

## Building the Container

Dynamic dependency injection comes with a cost of overhead. It is, however
possible to build a static container from a dynamic one. This is useful when
using the DIC in a production environment.

```php
<?php

use Selene\Components\DI\Builder;

$builder = new Builder($container);
$builder->build();
```

## Configuration

The DIC can be configured using file loaders

### Configuraion using xml

Example xml

```xml
<?xml version="1.0" encoding="UTF-8"?>

<container>
	<parameters>
		<parameter id="service.class">Acme\Foo</parameter>
	</parameters>

	<services>
		<service id="service" class="%service.class%"/>
	</services>
</container>

```

```php
<?php

use Selene\Components\DI\Builder;
use Selene\Components\DI\Loader\XmlLoader;
use Selene\Components\Config\Resource\Locator;

$loader = new XmlLoader($builder = new Builder($container), new Locator($paths));

$loader->load('services.xml');

```
