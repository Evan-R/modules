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
`Config` that sores all configuration values as an associative array:

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
your php loader would loock something like this:

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
use \Selene\Components\Config\Loader\DelegatingLoader;

$loader = new DelegatingLoader(new Resolver(
	new PhpLoader($config, $locator),	
	new JsonLoader($config, $locator)  
));

$loader->load('config.php', PhpLoader::LOAD_ALL);
$loader->load('config.json', PhpLoader::LOAD_ALL);

```
### Custom Loaders

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
        $contents = json_decode($file);

		// setter logic
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

Depending on the situation it may be useful to cache configuration.

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
	$loader->load('system.xml');
	$loader->load('database.xml');
	$loader->load('cache.xml');

	// …

	$cache->write("<?php\n    return ".var_export($config->all()).';');
}
```

[composer]: https://getcomposer.org
[repo_config]: https://github.com/seleneapp/config
