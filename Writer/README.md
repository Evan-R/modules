[![Build Status](https://travis-ci.org/seleneapp/writer.svg?branch=master)](https://travis-ci.org/seleneapp/writer)
[![Coverage Status](https://coveralls.io/repos/seleneapp/writer/badge.png)](https://coveralls.io/r/seleneapp/writer)

## Installation

Require `selene\writer` in your composer file.

```json
{
	"require": {
		"selene/writer": "dev-master"
	}
}
```

Then, run the composer install or update command.

```bash
$ composer install
```

## The Writer

Write a 2 line text block:

```php
<?php

use \Selene\Writer\Writer;

$writer = new Writer;

$writer
	->writeln('foo')
	->writeln('bar');

echo $write->dump();  //"foo\n    bar"  
```


The default indentation level is 4 spaces.

If you need a different level using spaces, you'll have to specify this on the
constructor:

```php
<?php

use \Selene\Writer\Writer;

$writer = new Writer(2);

$writer
	->writeln('foo')
	->writeln('bar');

echo $write->dump(); //"foo\n  bar"   
```

### API

Fluent methods:

- **writeln( `string|null $str` )**  
Adds a line.
 
- **indent( `void` )** 
Adds an indentation.

- **replaceln( string $str, int $index)**  
Replaces a line at a line index.

- **removeln( `int $index` )**  
Removes a line at a line index.

- **popln ( `void` )**  
Removes the last line.

- **appendln ( `string $str` )**  
Appends a string to the last line.

None fluent methods:

- **ignoreNull( `bool $ignore` )**  
Don't add a line if `$str` in `Writer::writeln()` is `null`. 

- **useTabs( `void` )**  
Use Tabs for indentation instead of spaces.

- **setOutputIndentation( `int $level` )**  
Sets the output indentation level of the whole text block.

- **getOutputIndentation( `void` )**  
Gets the output indentation level.

## Generators

There're three generator, `InterfaceWriter`, `ClassWriter`, and `TraitWriter`.  
All object generators share the following API:

### Shared API

- **setParent( `string $parent` )**  
This is a one time operation. Once the parent is set, you cannot change it. `$parent` name must be the FQN of the parent interface or class.

- **addUseStatement( `string $use` )**  
Adds a use statement to the php document. Naming conflicts will automatically
be resolved, however you can set your own alias by declating the import like
this `\Acme\Foo as FooAlias`. By default `Acme\Lib\Foo` will become `LibFoo`,
or `AcmeLibFoo`, or `AcmeLibFooAlias`, and so on. 
Note that the use statement is considered to be the FQN;

- **getImportResolver( )**  
Will return an instance of `Selene\Writer\Object\ImportResolver`.
This is useful if you need to know the aliases name of a imported string
(interface, trait, parent class or usestatement), e.g.

```php
<?php
$alias = $cg->getImportResolver()->getAlias('Acme\MyClass') // e.g. AcmeMyClassAlias;
```

- **addConstant( `Selene\Writer\Object\Constant $constant` )**  
Adds a constant to the interface.

- **addMethod( `Selene\Writer\Object\MethodInterface $method` )**  
Takes an object of type `Selene\Writer\Object\MethodInterface` and adds it to the object declaration.

- **getDoc()**  
Returns an instance of `Selene\Writer\Object\DocBlock` that represents the
document level docblock.

- **getObjDoc()**  
Returns an instance of `Selene\Writer\Object\DocBlock` that represents the
object level docblock.

- **noAutoGenerateTag()**  
By default, the objectwriter will add a timestamp to the document level
docblock. Use this if you wan't to deactivate this behavior.


### InterfaceWriter

Use this for autogenerating php interfaces.

```php
<?php 

use \Selene\Writer\Object\ClassWriter;

$iw = new InterfaceWriter('Foo', 'Acme', '\Acme\Parent');

file_put_contents('Acme/Foo.php', $iw->generate());

```
Results in:

```php
<?php 

/**
 * This file was generated at 2014-07-08 12:23:22.
 */

namespace Acme;

/**
 * @interface Foo
 * @see Acme\Parent
 */
interface Foo extends Parent
{
}
```

### API

- **addMethod( `Selene\Writer\Object\MethodInterface $method` )**  
Takes an object of type `Selene\Writer\Object\InterfaceMethod` and adds it to the interface.


### ClassWriter

Use this for autogenerating php classes.

```php
<?php 

use \Selene\Writer\Object\ClassWriter;

$cg = new ClassWriter('Foo', 'Acme');

file_put_contents('Acme/Foo.php', $cg->generate());

```
Results in:

```php
<?php 

/**
 * This file was generated at 2014-07-08 12:23:22.
 */

namespace Acme;

/**
 * @class Foo
 */
class Foo
{
}
```

###API

In addition to the InterfaceWriter:

- **addTrait( `string $trait` )**  
Takes a FQN of a trait and adds it as a trait. Traits will be automatically
added to the use statements list, except they're belong to exact same namespace of
the class.

- **addInterface( `string $interface` )**  
Adds an interface. Will be automatically added to the class imports. 

- ** setAbstract( `boolean $abstract` )**  
Toggle this class abstract.

- **addMethod( `MethodInterface $method` )**  
Takes an object of type `Method` and adds it to the class.

- **setProperties( `array $properties` )**   
Set the class properties. `$properties` must be an array of
`Selene\Writer\Object\Property` instances.

- **addProperty( `Selene\Writer\Object\Property $property` )**  
Takes an object of type `Selene\Writer\Object\Property` and adds it as a class property.

- **useTraitMethodAs(`string $trait`, `string $method`, `string $replacement`, `[string $visibility]`)**    
Replaces a method naming conflict between a trait an a class. Default visiblity
is `public`.

- **replaceTraitConflict(`string $trait`, `string $conflict`, `string $method`)**  
Replaces a method conflict between two traits.

### Example 

Generating a class with constants, methods, properties, and traits.

```php
<?php

use \Selene\Writer\Writer;
use \Selene\Writer\Object\Constant;
use \Selene\Writer\Object\Argument;
use \Selene\Writer\Object\Method;
use \Selene\Writer\Object\Property;
use \Selene\Writer\Object\ClassGenerator;

$cg = new ClassGenerator('Foo', 'Acme');

$cg->setParent('Acme\Lib\Bar');
$cg->addProperty(new Property('foo', 'string'));
$cg->addConstant(new Constant('T_ASW', '42'));
$cg->addMethod($method = new Method('__construct', Method::IS_PUBLIC, Method::T_VOID));

// declare method:
$method->setDescription('Constructor.')
$method->addArgument(new Argument('foo', Method::T_STRING, 'null'));
$method->setBody('$this->foo = $foo;');

// Add traits:
$cg->addTrait($foo = 'Acme\Lib\Traits\FooTrait');
$cg->addTrait($bar = 'Acme\Lib\Traits\BarTrait');
// resolve trait conflicts:
$cg->useTraitMethodAs($foo, 'getFoo', 'getFooStr', Method::IS_PRIVATE);
$cg->replaceTraitConflict($bar, $foo, 'getBar');

// modify the class doc.
$cg->getObjDoc()
	->setDescription('Some class.')
	->setLongDescription("Some more info on the class.\nSome more lines.")
	->addAnnotation('author', 'Thomas Appel <mail@thomas-appel.com>');

echo $cg->generate();

```
Results in

```php
<?php

/**
 * This file was generated at 2014-07-09 02:07:42. 
 */

namespace Acme;

use Acme\Lib\Bar;
use Acme\Lib\Traits\BarTrait;
use Acme\Lib\Traits\FooTrait;

/**
 * Some class.
 *
 * Some more info on the class.
 * Some more lines.
 *
 * @class Foo
 * @see Acme\Lib\Bar
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Foo extends Bar
{
	const T_ASW = 42;

    use FooTrait, 
		BarTrait {
		FooTrait::getFoo as private getFooStr;	
		BarTrait::getBar insteadof FooTrait;	
	}

    /**
     * foo
     *
     * @var mixed
     */
    string $foo;

    /**
	 * Constructor.
     *
     * @param string $foo
     */
    public function __construct($foo = null)
    {
        $this->foo = $foo;
    }
}
```
### TraitWriter

Behaves like the `ClassWriter` except there's no constants and interfaces.
