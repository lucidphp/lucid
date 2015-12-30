# Lucid Writer: dumping strings with elegance

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/signal-blue.svg?style=flat-square)](https://github.com/lucidphp/writer/tree/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/writer/blob/master/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/writer/master.svg?style=flat-square)](https://travis-ci.org/lucidphp/writer)
[![HHVM](https://img.shields.io/hhvm/lucid/writer/master.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/writer)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/writer/master.svg?style=flat-square)](https://coveralls.io/r/lucidphp/writer)

## Installation

```bash
$ composer require lucid/writer
```

## The Writer

### Dumping strings

Write a 2 line text block:

```php
<?php

use Lucid\Writer\Writer;

$writer = new Writer;

$writer
	->writeln('foo')
	->writeln('bar');

echo $writer->dump();  //"foo\n    bar"  
```


### Behavior

By defatault, the Writer will remove trailing spaces at the end of each line.

You may override this behavior by calling the `allowTrailingSpace()`
method.

```php
<?php

use Lucid\Writer\Writer;

$writer = new Writer;
$writer->allowTrailingSpace(true); // will now preserve trailing space chars for each line.
```

### Indentation

The default indentation level is 4 spaces.

If you require a different level using spaces, you'll have to specify this on the.
constructor:

```php
<?php

use Lucid\Writer\Writer;

$writer = new Writer(2);

$writer
	->writeln('foo')
	->writeln('bar');

echo $writer->dump(); //"foo\n  bar"   
```

You may also change spaces to tabs using the `useTabs()` method.

```php
<?php

use Lucid\Writer\Writer;

$writer = new Writer;
$writer->useTabs(true);

// …
```
### Output indentation

Output indentation indents the whole block and is applied just before the
string is being dumped. The value passed to `setOutputIndentation(int $level)`
acts as a multiplyer.  

### API

Fluent methods:

- **`Lucid\Writer\Writer` writeln( `string|null $str` )**  
Adds a line.
 
- **`Lucid\Writer\Writer` indent( `void` )** 
Adds an indentation.

- **`Lucid\Writer\Writer` replaceln( string $str, int $index)**  
Replaces a line at a line index.

- **`Lucid\Writer\Writer` removeln( `int $index` )**  
Removes a line at a line index.

- **`Lucid\Writer\Writer` popln ( `void` )**  
Removes the last line.

- **`Lucid\Writer\Writer` appendln ( `string $str` )**  
Appends a string to the last line.

None fluent methods:

- **`void` ignoreNull( `bool $ignore` )**  
Don't add a line if `$str` in `Writer::writeln()` is `null`. Default is on.

- **`void` allowTrailingSpace( `bool $space` )**  
Allow/Disallow traling space chars. Default is off.

- **`void` useTabs( `void` )**  
Use Tabs for indentation instead of spaces.

- **`void` setOutputIndentation( `int $level` )**  
Sets the output indentation level of the whole text block.  
The level value increments the indentation by one indent, e.g. `0` is no additional indentation, `1` is one indent, etc.  
Default is `0`.

- **`int` getOutputIndentation( `void` )**  
Gets the output indentation level. (see `Writer::setOutputIndentation()`);

## Generators

Dump PSR-2 compliant php source code.

There're three object generators, `InterfaceWriter`, `ClassWriter`, and `TraitWriter`.  
All object generators share a common API.

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
Will return an instance of `Lucid\Writer\Object\ImportResolver`.
This is useful if you need to know the aliases name of a imported string
(interface, trait, parent class or usestatement), e.g.

```php
<?php
$alias = $cg->getImportResolver()->getAlias('Acme\MyClass') // e.g. AcmeMyClassAlias;
```

- **`void` addConstant( `Lucid\Writer\Object\Constant $constant` )**  
Adds a constant to the interface.

- **`void` addMethod( `Lucid\Writer\Object\MethodInterface $method` )**  
Takes an object of type `Lucid\Writer\Object\MethodInterface` and adds it to the object declaration.

- **`Lucid\Writer\Object\DocBlock` getDoc( `void` )**  
Returns an instance of `Lucid\Writer\Object\DocBlock` that represents the
document level docblock.

- **`Lucid\Writer\Object\DocBlock` getObjDoc( `void` )**  
Returns an instance of `Lucid\Writer\Object\DocBlock` that represents the
object level docblock.

- **`void` noAutoGenerateTag( void )**  
By default, the objectwriter will add a timestamp to the document level
docblock. Use this if you wan't to deactivate this behavior.


### InterfaceWriter

Use this for autogenerating php interfaces.

```php
<?php 

use Lucid\Writer\Object\ClassWriter;

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

- **addMethod( `Lucid\Writer\Object\MethodInterface $method` )**  
Takes an object of type `Lucid\Writer\Object\InterfaceMethod` and adds it to the interface.


### ClassWriter

Use this for autogenerating php classes.

```php
<?php 

use Lucid\Writer\Object\ClassWriter;

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

- **`void` addTrait( `string $trait` )**  
Takes a FQN of a trait and adds it as a trait. Traits will be automatically
added to the use statements list, except they're belong to exact same namespace of
the class.

- **`void` addInterface( `string $interface` )**  
Adds an interface. Will be automatically added to the class imports. 

- **`void` setAbstract( `boolean $abstract` )**  
Toggle this class abstract.

- **`void` addMethod( `MethodInterface $method` )**  
Takes an object of type `Method` and adds it to the class.

- **`void` setProperties( `array $properties` )**   
Set the class properties. `$properties` must be an array of
`Lucid\Writer\Object\Property` instances.

- **`void` addProperty( `Lucid\Writer\Object\Property $property` )**  
Takes an object of type `Lucid\Writer\Object\Property` and adds it as a class property.

- **`void` useTraitMethodAs(`string $trait`, `string $method`, `string $replacement`, `[string $visibility]`)**    
Replaces a method naming conflict between a trait an a class. Default visiblity
is `public`.

- **`void` replaceTraitConflict(`string $trait`, `string $conflict`, `string $method`)**  
Replaces a method conflict between two traits.

### Example 

Generating a class with constants, methods, properties, and traits.

```php
<?php

use Lucid\Writer\Writer;
use Lucid\Writer\Object\Constant;
use Lucid\Writer\Object\Argument;
use Lucid\Writer\Object\Method;
use Lucid\Writer\Object\Property;
use Lucid\Writer\Object\ClassGenerator;

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

Behaves like the `ClassWriter` except there're no constants and interfaces.

#### Notes

Setting method bodies is up to you. However, if you rely on class base names
that have been imported you can utilize the import resolver to determine the
actual shortname that's used on the object writer.

Also see the [Shared API](#shared-api) section.

```php
<?php

// $cl beeing the object writer instance.

$body = 'return new '.$cl->getImportResolver()->getAlias('Acme\MyClass').';';
$method->setBody($body);

```
