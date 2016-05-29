# Shared utilities for lucid/* packages

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/signal-blue.svg?style=flat-square)](https://github.com/lucidphp/common/tree/develop)
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

$ints.each(function (int $int) {
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
