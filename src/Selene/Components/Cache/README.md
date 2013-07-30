# Stream Cache
## Caching Library for streamphp
[![Build Status](https://api.travis-ci.org/streamphp/cache.png?branch=master)](https://travis-ci.org/streamphp/cache)


### Usage

#### Initializing cache

```php
<?php
use Stream\Cache\Storage;

$cache = new Storage($cacheDriver);

```

The cachedriver instance must implement the `Stream\Cache\Interfaces\Driver` interface.

setup using the filessystem driver:

```php
<?php

use Stream\Cache\Storage;
use Stream\Cache\Driver\DriverFilesystem;
use Stream\Filesystem\FSDirectory;

$cacheDriver = new DriverFilesystem(new FSDirectory('/path/to/cachelocation'));
$cache = new Storage($cacheDriver);
```

setup using the memcached driver:

```php 
<?php

use Stream\Cache\Storage;
use Stream\Cache\Driver\ConnectionMemcached;
use Stream\Cache\Driver\DriverMemcached;
use \Memcached;

$connection = new ConnectionMemcached(new \Memcached('fooish'));

$memcached = $connection->init(array(
    array(
        'host' => '127.0.0.1',
        'port' => 11211,
        'weight' => 100
    )
));
        
$cacheDriver = new DriverMemcached($memcached);

$cache = new Storage($cacheDriver);

```

#### Caching items

`Storage::write()` takes four arguments: `cacheid`, `data`, `expiry` and `compressed`: 

```php
$cache->write('cacheid', $itemToBeCached, 3600, true);
```

Cache an item with a far future expiry date:

```php
$cache->seal('cacheid', $itemToBeCached, true);
```

#### Check if an item is cached

```php
if ($cache->has('cacheid')) {
	$data = $cache->read('cacheid');
} else {
	// fetch $data elsewhere
}
``` 

#### Delete a cached item or purge all cached data

```php
$cache->purge('cacheid'); // delete this cached entry
$cache->purge();          // delete all  cached items 
```