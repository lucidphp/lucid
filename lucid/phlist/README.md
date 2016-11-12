# Python List-Like Data Structure For php.

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/phlist-blue.svg?style=flat-square)](https://github.com/lucidphp/phlist/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/phlist/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/phlist/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/phlist)
<!--
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/phlist/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/phlist)
-->
<!--
[![HHVM](https://img.shields.io/hhvm/lucid/phlist/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/phlist)
-->

## Requirements

```
php >= 7.1
```

## Installation

```bash
$ composer require lucid/phlist
```


## Phlist class
The `Phlist` class implements a `ListInterface` and is modelled after pythons `list`.

```php
<?php

use Lucid\Phlist\Phlist;

$list = new Phlist('foo', 'bar', ...);
```

## ListInterface API

`ListInterface ListInterface::push(mixed $value)`

```php
<?php
$list = new Phlist('foo');
$list->push('bar'); 
$list->toArray(); // => ['foo', 'bar']
```

`ListInterface ListInterface::insert(int $index, mixed $value)`

```php
<?php
$list = new Phlist(1, 2, 3);
$list->insert(1, 1.5); 
$list->toArray(); // => [1, 1.5, 2]
```

`mixed ListInterface::pop(void)`

```php
<?php
$list = new Phlist(1, 2, 3);
$list->pop(); // => 3
```

`ListInterface ListInterface::remove(mixed $value)`

```php
<?php
$list = new Phlist(1, 2, 3);
$list->remove(2); 
$list->toArray(); // [1, 3]
```

`ListInterface ListInterface::sort(callable $sort|null)`

```php
<?php
$list = new Phlist(1, 4, 3, 2);
$list->sort(); 
$list->toArray(); // [1, 2, 3, 4]

$list = new Phlist(1, 4, 3, 2);
$list->sort(function ($a, $b) {
    return $a > $b ? 1 : -1;
}); 
$list->toArray(); // [1, 2, 3, 4]

```

`ListInterface ListInterface::reverse(void)`

```php
<?php
$list = new Phlist(1, 2, 3);
$list->reverse();
$list->toArray(); // =>[3, 2, 1]
```

`ListInterface ListInterface::countValue(mixed $value)`

```php
<?php
$list = new Phlist(1, 2, 3, 4, 2, 5);
$list->countValue(5); // => 1
$list->countValue(2); // => 2
```

`ListInterface ListInterface::extend(ListInterface $list)`

```php
<?php
$listA = new Phlist('a', 'b');
$listB = new Phlist('c', 'd');
$listA->extend($listB);
$listA->toArray() // => ['a', 'b', 'c', 'd'];
```
