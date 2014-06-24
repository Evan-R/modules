#The common component

[![Build Status](https://api.travis-ci.org/seleneapp/common.png?branch=development)](https://travis-ci.org/seleneapp/common)
[![Latest Stable Version](https://poser.pugx.org/selene/common/v/stable.png)](https://packagist.org/packages/selene/common) 
[![Latest Unstable Version](https://poser.pugx.org/selene/common/v/unstable.png)](https://packagist.org/packages/selene/common) 
[![License](https://poser.pugx.org/selene/common/license.png)](https://packagist.org/packages/selene/common)
[![HHVM Status](http://hhvm.h4cc.de/badge/selene/common.png)](http://hhvm.h4cc.de/package/selene/common)

The Common component is shared across almost ever other selene component.

## Data Structures

### List

Lists are inspired by python lists

#### Usage

```php
<?php

use \Selene\Components\Common\Data\BaseList;

$list = new BaseList(1, 2, 3);

// do operations

$values = $list->toArray();

```
#### append() 

Adding values to the end of the list

```php
<?php

$list->append('foo');     // [1, 2, 3, 'foo']
// or
$list->add('foo');        // [1, 2, 3, 'foo']
```
#### insert()

Inserting a value a a specified index

```php
<?php

$list->insert(4, 3);      // [1, 2, 3, 4, 'foo']
```

#### pop()

Return the last value and remove it from the list.

```php
<?php

$list->pop();             // 'foo' 
```

#### pop($index)

Return a value at a specified index remove it from the list.

```php
<?php

$list->pop(2);            // 3 
```

#### remove($value)

Remove a value from the list.

```php
<?php

$list->remove(1);         // [2, 4] 

```

#### countValue($value)

Count the occurance of a value.

```php
<?php

$list->countValue(2);     // 1 
```

#### sort()

Sort all values.

```php
<?php

$list->sort();
```

#### reverse()

Reverse the list order.

```php
<?php

$list->reverse();         // [4, 2]
```

#### extend($list)

Extend a list with a nother.

```php
<?php

$newList = new BaseList('foo', 'bar');

$list->extend($newList);  
```
### Collection

```php
<?php

use \Selene\Components\Common\Data\Collection;

$collection = new Collection;

```

## Traits

### Getter

The `Getter` trait contains a few accessor method for dealing with retreiving
data from an array.

#### getDefault($data, $key, $defaultValue [optional])

Return a value from a dataset if the value is set for a given key, otherwies return a default
value.

```php
<?php

	use Getter;

	private $attributes = [];

	public function get($attribute, $default = null);
	{
		return $this->getDefault($this->attributes, $attribute, $default);
	}

```

#### getDefaultUsingKey($data, $key, $defaultValue [optional])

Return a value from a dataset if the key is present, otherwies return a default
value.

```php
<?php

	use Getter;

	private $attributes = [];

	public function get($attribute, $default = null);
	{
		return $this->getDefaultUsingKey($this->attributes, $attribute, $default);
	}

```

#### getDefaultUsing($data, $key, $closure [optional])

Return a value from a dataset if the value is set for a given key, otherwies return a default
value using a callback function.

```php
<?php

	use Getter;

	private $attributes = [];

	public function get($attribute);
	{
		return $this->getDefaultUsing($this->attributes, $attribute, function () {
			// get some default value
			return $result;		
		});
	}

```

#### getDefaultArray($data, $key, $defaultValue [optional])

Return a value from a multi-dimensional dataset if the key is present, otherwies return a default
value. You can specify a key delimitter to access array keys, e.g. `foo.bar`
will search for a value in `['foo' => ['bar' => 12]]`

```php
<?php

	use Getter;

	private $attributes = [];

	public function get($attribute);
	{
		return $this->getDefaultArray($this->attributes, $attribute, $default, '.');
	}

```
