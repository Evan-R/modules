# Selene Component for loading, parsing and, writing xml

## The Parser

The `Parser` class can parse xml string, files, DOMDocuments, and DOMElements
to a php array. 


### Parsing xml strings
```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->parse('<data><foo>bar</foo></data>');

```

### Parsing xml files

```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->parse('/path/to/data.xml');

```

### Parsing a `DOMDocument`

```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->parseDom($dom);

```

### Parsing a `DOMElement`

```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->parseDomElement($element);

```

## Parser Options

### Merge attributes


```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->setMergeAttributes(true);

```

### Set the attributes key

If attribute merging is disabled, use this to change the default attributes key
(default is `@attributes`).


```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->setAttributesKey('@attrs');

```

### Set index key

This forces the parser to treat nodes with a nodeName of the given key to be
handled as list. 


```php

use \Selene\Components\Xml\Parser;

$parser = new Parser;

$parser->setIndexKey('item');

```

### Set a pluralizer

By default the parser will parse xml structures like


```xml
<entries>
	<entry>1</entry>
	<entry>2</entry>
</entries>

```

To something like:

```php
['entries' => ['entry' => [1, 2]]]
```

Setting a pluralizer can fix this. 

Note, that a pluralizer can be any callable that takes a string and returns
a string.


```php

$parser->setPluralizer(function ($string) {
	if ('entry' === $string) {
		return 'entries';
	}
});

```

```php
	['entries' => [1, 2]]
```
