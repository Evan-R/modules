# Config component for selene.

[![Build Status](https://api.travis-ci.org/seleneapp/config.png?branch=development)](https://travis-ci.org/seleneapp/config)
[![Code Climate](https://codeclimate.com/github/seleneapp/config.png)](https://codeclimate.com/github/seleneapp/config)
[![Coverage Status](https://coveralls.io/repos/seleneapp/config/badge.png?branch=development)](https://coveralls.io/r/seleneapp/config?branch=development)

[![License](https://poser.pugx.org/selene/config/license.png)](https://packagist.org/packages/selene/config)

## Installation

The component can be installed via [composer][composer].

```json
{
	"require":{
		"selene/config":"dev-development"
	}
}
```
Then run 

```bash
$ composer install
```
## Loaders

The [config package][repo_config] contains three different base loaders, the `PhpFileLoader`, the `XmlFileLoader`, and the `CallableLoader`. 
Both, the `PhpFileLoader` and the `XmlFileLoader` extend `FileLoader` and
require a resource locator instance. 

The loading implementation is up to you. e.g. let's say you have a class
`Config` that sores all configuration values as an associative array.

```php
<?php

namespace Acme\Config;

class Config
{
	protected $config;

	public function __construct(array $config = [])
	{
		$this->config = $config;
	}

	public function addValues(array $values)
	{
		$this->config = array_merge((array)$this->config, $values);
	}

	public function get($key, $default = null)
	{
		if (…) {
			return $value;
		}

		return $default;
	}

	public function all()
	{
		return $this->config;
	}
}
```
Your php loader would loock something like this:

```php
<?php

namespace Acme\Config;

use \Selene\Components\Config\Loader\PhpFileLoader;
use \Selene\Components\Config\Resource\LocatorInterface;

class PhpLoader extends PhpFileLoader
{
	public function __construct(Config $config, LocatorInterface $locator)
	{
		$this->config = $config;
		parent__construct($locator);
	}

	protected function doLoad($file)
	{
		$values = parent::doLoad($file);

		$this->config->addValues($values);
	}
}
```
### Loading files

With your new loader, you're now able to load configuration files like this:

```php
<?php

use \Acme\Config\Config;
use \Acme\Config\PhpLoader;
use \Selene\Components\Config\Resource\Locator;

$config  = new Config;
$locator = new Locator(['path/config/a', 'path/config/b']);
$loader  = new PhpLoader($config, $locator;

$loader->load('config.php');
```

Note, that the locator takes an array of paths where to locate the
configuration files. By default, the first result is used and the remaining
paths are being discared, however if you pass a boolean (true) to the `load()`
mehtod as a second argument, files from all paths will be located and loaded.  

```php
<?php

$loader->load('config.php', PhpLoader::LOAD_ALL);
```

### Loading different resource types

It is possible to load different types of configuration using the
`DelegatingLoader` class. 

```php
<?php

use \Acme\Config\PhpLoader;
use \Acme\Config\JsonLoader;
use \Selene\Components\Config\Loader\Resolver;
use \Selene\Components\Config\Loader\DelegatingLoader;

$loader = new DelegatingLoader(new Resolver(
	new PhpLoader($config, $locator),	
	new JsonLoader($config, $locator)  
));

$loader->load('config.php', PhpLoader::LOAD_ALL);
$loader->load('config.json', PhpLoader::LOAD_ALL);

```
### Custom Loaders

In theory, you can create all kinds of loaders, e.g. a JSON loader.

```php
<?php

namespace Acme\Config;

use \Selene\Components\Config\Loader\FileLoader;

class JsonFileLoader extends FileLoader
{
    protected $extension = 'json';

    /**
     * {@inheritdoc}
     */
    protected function doLoad($file)
    {
        $contents = json_decode(file_get_contents($file), true);

		// setter logic goes here.
    }
}
```

### Listening to loader events.

Every time a resource is loaded, the loader will notify listeners that have
been registered on the loader. Listeners must implements the
`Selene\Components\Config\Loader\LoaderListener`.

For example, you may want to log the loading event to yout application log
file. 

```php
<?php

namespace Acme\Config;

use \Psr\Log;
use \Selene\Components\Config\Loader\LoaderListener;

class ConfigLoaderLogger extends LoaderListener
{
	public function __construct(Log $logger)
	{
		$this->logger = $logger;
	}

	public function onLoaded($resource)
	{
		$this->logger->info(sprintf('Loaded resource "%s"',  $resource));
	}
}


```

Simply add the listener to your config loader using the `addListener()` method.

```php
<?php

$configLoaderLogger = new ConfigLoaderLogger($logger);

$loader->addListener($configLoaderLogger);

```
If you need to remove the listener, you can user the `removeListener` method.

```php
<?php

$loader->removeListener($configLoaderLogger);

```

## Caching

Depending on the situation it may be useful to cache configuration, e.g.
parsing an xml file on each request will affect performance. Instead
you probably want to parse the configuration file once and store its contents
in a php array. 

```php
<?php

use \Acme\Config\Config;
use \Acme\Config\XmlLoader;
use \Selene\Components\Config\Cache;
use \Selene\Components\Config\Resource\Locator;

$cache = new Cache($file);

if ($cache->isValid()) {
	$config = new Config(include $file);
} else {
	$config = new Config;
	$loader = new XmlLoader($config, new Locator($paths));
	$loader->load('config.xml');

	// …

	$cache->write("<?php\n    return ".var_export($config->all()).';');
}
```

The above solution is fine for loading a single file. `Cache::isValid()` will report `false` if
the given cache file has been modified since the last request. 
However, the actual configuration files won't be taken into account.

The cache is capable of resource checking. Simply pass `true` to the constructr as a second argument 
and supply a list of resources to be tracked when writing the cache.

This is where the event capability of the loader comes into play. 
Lets modify the `Acme\Config\Config` class by implementing the `Loaderlistener`
interface.

```php
<?php

namespace Acme\Config;

use \Selene\Components\Config\Loader\PhpFileLoader;
use \Selene\Components\Config\Loader\LoaderListener;
use \Selene\Components\Config\Resource\FileResource;
use \Selene\Components\Config\Resource\ObjectResource;
use \Selene\Components\Config\Resource\LocatorInterface;

class PhpLoader extends PhpFileLoader implements LoaderListener
{
    private $resources;

	//…

	public function onLoaded($resource)
	{
		if (is_object($resource)) {
			$this->resources[] = new ObjectResource($resource);	
		} elseif(is_string($file)) {
			$this->resources[] = new FileResource($resource);	
		}
	}

	public function getResources()
	{
		return $this->resources;
	}
}
```
Every resource that's being loaded is now pushed to an array that we can use as
the list of files the cache will use to track for cachnges. 

```php
<?php

$cache = new Cache($file, true);

// do the loading proceedure. 

// write the cache file content and pass in the resources to be tracked.
$cache->write(
	"<?php\n    return ".var_export($config->all()).';',
	$config->getResources()
);

```

## Validation

The validator allows you to validate associative arrays. This is useful for
validating user defined configuration before loading it. 

The validator comes with a `Builder` class which allows you to define your
configuration strcucture as a tree. The tree then will be validated agains an
input array.

```php
<?php

use \Selene\Components\Config\Validator\Builder;

$builder = new Builder('config');
$builder
	->getRoot()
	// build the tree


$validator = $builder->getValidator();
$validator->load($config);
$validator->validate();

```

### Node types

There're two different node types. **ScalarNodes** and **ArrayNodes**.

Both share common methods for defining their behaviour on validation time. 

- **optional()**    
Marks the node to be required.

- **notEmpty()**   
Marks the node **not** to be not empty. The definition of an empty value depends on the
node type. e.g. on a string node, an empty string is an empty value.

- **defaultValue($value)**  
The value used if the node is optional and missing, or empty. 
 
- **condition()**  
Starts a conditional block. See [Conditions conditions](#Node conditions) below.

- **end()**  
When in context of the builder, `end()` will return the parent node of the
current one.

#### Scalar nodes

Scalar nodes represents scalar values like strings, booleans, integers. etc.

Unlike ArrayNodes, scalar nodes cannot have childnodes. 

##### boolean

Represents a boolean value.	

```php
<?php

$builder
	->getRoot()
	->boolean('required')
	->end();
```

##### string

Represents a string value.	

```php
<?php

$builder
	->getRoot()
	->string('name')
	->end();
```
##### integer

Represents an integer.	

```php
<?php

$builder
	->getRoot()
	->integer('port')
	->end();
```
##### float

Represents a float value.	

```php
<?php

$builder
	->getRoot()
	->float('precission')
	->end();
```
	
#### Array nodes

ArrayNodes may contain child nodes of both, type scalar and type array. 

##### dict

Represents an associative array.	

```php
<?php

$builder
	->getRoot()
	->dict('memcached_server')
		->string('host')->end()
		->integer('port')->end()
		->integer('weight')->end()
	->end();
```
##### values

Represents an indexed array.	

Note that you only can define one child node on a value node.  
The childnode then represents the value type that's supposed to be in that
indexed array. 

```php
<?php

$builder
	->getRoot()
	->values('paths')
		->string('path')->end()
	->end();
```

#### Node conditions

Node conditions act as a simple if/then block. The "then" part is only executed
if the "if" part return true.   

```php
<?php

$root
	->string()
		->condition()
			->when(function ($value) {…})
			->then(function ($value) {…})
		->end()
	->end();
```
There're a couple if predefined "if" conditions.

- **always()**  
Will always be execued.

- **ifTrue()**  
Same as `when`.

- **ifIsMissing()**  
Will only trigger if the value (speaking the key of the input) is missing.

- **ifIsEmpty()**  
Will only trigger if the value is emtpy.

- **ifIsNull()**  
Will only trigger if the value is null.

- **ifIsArray()**  
Same as `when`.
Will only trigger if the input value is an array.

- **ifIsNotArray()**  
Will only trigger if the input value is not an array.

- **ifIsInArray(array $values)**  
Will only trigger if the input value is in the `$values` array.

- **ifIsNotInArray()**  
Same as `when`.
Will only trigger if the input value is not in the `$values` array.

- **ifIsString()**  
Will only trigger if the input value is a string.

There're also a couple if predefined "then" results.

- **thenMarkInvalid**  
Marks the node invalid.

- **thenUnset**  
Unsets the current node an removes it from its parent node.

- **thenEmptyArray**  
Will return an empty array.

[composer]: https://getcomposer.org
[repo_config]: https://github.com/seleneapp/config
