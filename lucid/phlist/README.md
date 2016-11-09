# Python List-Like Data Structure For php.

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/phlist-blue.svg?style=flat-square)](https://github.com/lucidphp/phlist/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/phlist/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/phlist/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/phlist)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/phlist/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/phlist)
[![HHVM](https://img.shields.io/hhvm/lucid/phlist/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/phlist)

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
