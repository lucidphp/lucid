# A Collection Type

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/collection-blue.svg?style=flat-square)](https://github.com/lucidphp/collection/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/collection/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/collection/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/collection)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/collection/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/collection)
[![HHVM](https://img.shields.io/hhvm/lucid/collection/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/collection)

## Requirements

```
php >= 7.1
```

## Installation

```bash
$ composer require lucid/collection
```


## Collections

### Creating typed collections


```php
<?php

namespace Acme\Data;

use Lucid\collection\AbstractCollection;

class Integers extends AbstractCollection
{
    private $ints;
    
    public function __construct(int ...$integers)
    {
        $this->ints = $integers;
    }
    
    public function reduce(callable $reduce) : int
    {
        return parent::reduce($reduce);
    }

    protected function getData() : array
    {
        return $this->ints;
    }
}
```

##### Collection methods

**`map`** : `Lucid\collection\Struct\CollectionInterface`

Returns a new Collection containing the mapped values of the origin collection.

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->map(function(int $num) {
    return $num * $num;
})->toArray();

// => [1, 4, 9, 16, 25, 36] 
```

**`filter`** : `Lucid\collection\Struct\CollectionInterface`

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

**`slice`** : `Lucid\collection\Struct\CollectionInterface`

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

**`head`** : `Lucid\collection\Struct\CollectionInterface`

Returns a new Collection containing the top portion of the original collection. `CollectionInterface::head()` also takes an optional argument `int $max`. 

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->head()->toArray();
// => [1] 

$res = $ints->head(3)->toArray();
// => [1, 2, 3] 
```

**`tail`** : `Lucid\collection\Struct\CollectionInterface`

Returns a new Collection containing the tail portion of the original collection. `CollectionInterface::tail()` also takes an optional argument `int $max`.

```php
<?php

$ints = new Integers(1, 2, 3, 4, 5, 6);


$res = $ints->tail()->toArray();
// => [2, 3, 4, 5, 6] 

$res = $ints->tail(3)->toArray();
// => [4, 5, 6] 
```
**`each`** : `Lucid\collection\Struct\CollectionInterface`

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