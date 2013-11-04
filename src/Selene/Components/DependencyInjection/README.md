## Setup

```php
<?php

use Selene\Component\DependencyInjection;

$container = new Container;
```

## Usage

### Parameters

```php
<?php

$container->setParam('my.param', 'param value');
$container->getParam('@my.param');
```
### Creating services using factories

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
	->setService('foo_service', null)
	->setFactory('@foo.factory', 'makeFooService')
	->addArgument('@foo.options');

```
