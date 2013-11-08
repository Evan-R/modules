[![Build Status](https://api.travis-ci.org/seleneapp/cache.png?branch=development)](https://travis-ci.org/seleneapp/cache)

# Setup

```php
<?php

use Selene\Components\Cache\Storage;

$cache = new Storage($driver);
```

# Usage

```php
<?php

use Selene\Components\Cache\Storage;

$cache->set('cache_key', $cacheValue, 1000);

$cache->get('cache_key');
```
