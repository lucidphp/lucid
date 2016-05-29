# Shared utilities for lucid/* packages

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/common-blue.svg?style=flat-square)](https://github.com/lucidphp/common/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/common/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/common/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/common)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/common/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/common)
[![HHVM](https://img.shields.io/hhvm/lucid/common/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/common)

## Requirements

```
php >= 7.0
```

## Installation

```bash
$ composer require lucid/common
```

## Traits

### Getter

**`getDefault`** : `mixed`

Gets a value by key and returns a given default value if the key does not exist, meaning that  [`$key => null`] will return null and not the default value.

```php
<?php

use Lucid\Common\Traits\Getter;

class Person
{
    use Getter;
    
    // ...    
    
    public function getname() : string
    {
        return $this->getDefault($this->data, 'name', 'Doe');
    }
}
```

**`getDefaultUsing`** : `mixed`

Like `getDefault()` but takes a user callback to retrieve the default value.

```php
<?php

use Lucid\Common\Traits\Getter;

class Person
{
    use Getter;
    
    // ...    
    
    public function getname() : string
    {
        return $this->getDefault($this->data, 'name', function () {...});
    }
}
```

**`getStrictDefault`** : `mixed`

Like `getDefault` but checks if the key is set.

**`getStrictDefaultUsing`** : `mixed`

Like `getDefaultUsing` but checks if the key is set.


## Helpers

### String helper

**`safeCmp`** : `bool`

Safe compares two strings.

```php
<?php

use Lucid\Common\Helper\Str;

Str::safeCmp($strA, $strB);

```

**`equals`** : `bool`

Compares two strings.

```php
<?php

use Lucid\Common\Helper\Str;

Str::equals('foo', 'bar');

// false

Str::equals('foo', 'foo');

// true

```

**`rand`** : `string`

Creates a random string of a give length `int $length` using `openssl_random_pseudo_bytes`.

```php
<?php

use Lucid\Common\Helper\Str;

echo Str::rand(5);

// a random string of length 5.

```
**`quickRand`** : `string`
The fallback method for `str::rand()` if `openssl_random_pseudo_bytes` is not available.

```php
<?php

use Lucid\Common\Helper\Str;

echo Str::rand(5);

// a random string of length 5.

```


**`snakeCase`** : `string`

Transforms a string to a "snakecased" representation.
`Str::snameCase` also takes a second argument `string $delim` which defaults to
`_` containing the default delimiter

```php
<?php

use Lucid\Common\Helper\Str;

echo Str::snakeCase('fooBar');

// 'foo_bar'

echo Str::snakeCase('fooBar', '-');

// 'foo-bar'


```

**`camelCase`** : `string`

Transforms a string to a camel cased representation.
`Str::camelCase` also takes a second argument `array $replacements` which defaults to
`['-', '_']` containing the default delimiter

```php
<?php

use Lucid\Common\Helper\Str;

echo Str::camelCase('foo_bar');

// 'fooBar'

echo Str::camelCase('foo-bar');

// 'fooBar'

echo Str::camelCase('foo.bar', ['.']);

// 'fooBar'

```

**`camelCaseAll`** : `string`

Like `Str::camelCase` but uppercases the first letter.

```php
<?php

use Lucid\Common\Helper\Str;

echo Str::camelCaseAll('foo_bar');

// 'FooBar'

```

### Array helper

**`flatten`** : `array`

Recursively flattens a multidimensional array.

**`column`** : `array`

Returns the values from a single column in the input array.
See http://php.net/manual/en/function.array-column.php

```php
<?php

use Lucid\Common\Helper\Arr;

$in = [
            [
                'id' => '12',
                'name' => 'rand',
                'handle' => 'xkd23',
            ],
            [
                'id' => '14',
                'name' => 'band',
                'handle' => 'xkd25',
            ],
            [
                'id' => '22',
                'name' => 'land',
                'handle' => 'xkd77',
            ],
        ];
$out = Arr::column($in, 'id');

// ['12', '14', '22'];

$out = Arr::column($in, 'id', 'handle');

// ['xkd23' => '12', 'xkd25' => '14', 'xkd77' => '22'];

```
**`column`** : `array`

Returns the values from a single column in the input array.
See http://php.net/manual/en/function.array-column.php

```php
<?php

use Lucid\Common\Helper\Arr;

$in = [
            [
                'id' => '12',
                'name' => 'rand',
                'handle' => 'xkd23',
            ],
            [
                'id' => '14',
                'name' => 'band',
                'handle' => 'xkd25',
            ],
            [
                'id' => '22',
                'name' => 'land',
                'handle' => 'xkd77',
            ],
        ];
$out = Arr::column($in, 'id');

// ['12', '14', '22'];

$out = Arr::column($in, 'id', 'handle');

// ['xkd23' => '12', 'xkd25' => '14', 'xkd77' => '22'];

```

**`pluck`** : `array`

Plucks values by key from a given list of objects or arrays.

```php
<?php

use Lucid\Common\Helper\Arr;

Arr::pluck([['name' => 'Charly'], ['name' => 'Jessica']], 'name');
// ['Charly', 'Jessica']

$obj1 = new stdClass;
$obj1->name = 'Charly';

$obj2 = new stdClass;
$obj2->name = 'Jessica';

Arr::pluck([$obj1, $obj2], 'name');
// ['Charly', 'Jessica']

```

**`zip`** : `array`

Zips two or more arrays.

```php
<?php

use Lucid\Common\Helper\Arr;

Arr::zip(['moe', 'larry', 'curly'], [30, 40, 50], [true, false, false]);
// [["moe", 30, true], ["larry", 40, false], ["curly", 50, false]]
```

**`compact`** : `array`

Filters "truthy" values from a given input array.

```php
<?php

use Lucid\Common\Helper\Arr;

Arr::compact([0, 1, 'yep', true, null]);
// [1, 'yep', true]
```

**`max`** : `int`

Returns the greatest length from a give set of arrays.

```php
<?php

use Lucid\Common\Helper\Arr;

Arr::max(['a', 'b', 'c'], ['A', 'C'], [1, 2, 3, 4]);
// 4
```
**`min`** : `int`

Returns the lowest length from a give set of arrays.

```php
<?php

use Lucid\Common\Helper\Arr;

Arr::min(['a', 'b', 'c'], ['A', 'C'], [4, 5, 6]);
// 2
```

**`get`** : `mixed`

Finds values in multidimensional arrays.

```php
<?php

use Lucid\Common\Helper\Arr;

echo Arr::get(['foo' => ['bar' => 'baz']], 'foo.bar');
// 'baz'

echo Arr::get(['foo' => ['bar' => 'baz']], 'foo:bar', ':');
// 'baz'
```

**`set`** : `array`

Sets a value in a multidimensional array.

```php
<?php

use Lucid\Common\Helper\Arr;

$arr = ['foo' => ['bar' => 'baz']]

Arr::set($arr, 'foo.bar', 'bam');
// ['foo' => ['bar' => 'bam']]
```
**`unsetKey`** : `array`

Unsets a value in a multidimensional array.

```php
<?php

use Lucid\Common\Helper\Arr;

$arr = ['foo' => ['bar' => 'baz']]

Arr::unsetKey($arr, 'foo.bar');
// ['foo' => []]
```
## Data Structs

### Collections

#### Creating typed collections


```php
<?php

namespace Acme\Data;

use Lucid\Common\Struct\AbstractCollection;

class Integers extends AbstractCollection
{
    private $ints;
    
    public function reduce(callable $reduce) : int
    {
        return parent::reduce($reduce);
    }

    protected function getData() : array
    {
        return $this->ints;
    }

    protected function setData(int ...$data)
    {
        $this->ints = $data;
    }

    protected function getSetterMethod() : string
    {
        return 'setData';
    }
}
```

##### Collection methods

**`map`** : `Lucid\Common\Struct\CollectionInterface`

Returns a new Collection containing the mapped values of the origin collection.

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->map(function(int $num) {
    return $num * $num;
})->toArray();

// => [1, 4, 9, 16, 25, 36] 
```

**`filter`** : `Lucid\Common\Struct\CollectionInterface`

Returns a new Collection containing the filtered values of the origin collection.
`CollectionInterface:filter()` also takes an optional second argument `int $flag` which can be either `CollectionInterface::FILTER_USE_KEY` to filter by value keys, or `CollectionInterface::FILTER_USE_BOTH`, to filter by key and value.

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->filter(function(int $num) {
    return $num > 3;
})->toArray();

// => [4, 5, 6] 
```

**`slice`** : `Lucid\Common\Struct\CollectionInterface`

Returns a new Collection containing a slice of the original collection. 

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->slice(2)->toArray();
// => [2, 4, 5, 6] 

$res = $ints->slice(2, 2)->toArray();
// => [2, 4]

$res = $ints->slice(-1)->toArray();
// => [6]
```

**`head`** : `Lucid\Common\Struct\CollectionInterface`

Returns a new Collection containing the top portion of the original collection. `CollectionInterface::head()` also takes an optional argument `int $max`. 

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->head()->toArray();
// => [1] 

$res = $ints->head(3)->toArray();
// => [1, 2, 3] 
```

**`tail`** : `Lucid\Common\Struct\CollectionInterface`

Returns a new Collection containing the tail portion of the original collection. `CollectionInterface::tail()` also takes an optional argument `int $max`.

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->tail()->toArray();
// => [6] 

$res = $ints->tail(3)->toArray();
// => [4, 5, 6] 
```
**`each`** : `Lucid\Common\Struct\CollectionInterface`

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);

$ints->each(function (int $int) {
    echo $int;
});

// you can also do
foreach ($ints as $int) {
    echo $int;
}
```

### Items
The `Items` class implements a `ListInterface` and is modeled after pythons `list`.


```php
<?php

use Lucid\Common\Struct\Items;

$list = new Items('foo', 'bar', ...);
```
