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
parsing an xml file on each request is likely a huge performance hit. Instead
you probably want to parse the configuration file once and store its contents
as a php array. 

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


[composer]: https://getcomposer.org
[repo_config]: https://github.com/seleneapp/config
