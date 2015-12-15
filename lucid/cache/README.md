# Caching library.

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/signal-blue.svg?style=flat-square)](https://github.com/lucidphp/cache/tree/local-dev)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/cache/blob/local-dev/LICENSE.md)

[![Build Status](https://img.shields.io/travis/iwyg/cache/local-dev.svg?style=flat-square)](https://travis-ci.org/lucidphp/cache)
<!--
[![Code Coverage](https://img.shields.io/coveralls/iwyg/cache/local-dev.svg?style=flat-square)](https://coveralls.io/r/lucidphp/cache)
-->
[![HHVM](https://img.shields.io/hhvm/lucid/cache/local-dev.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/cache)

## Requirements
```
php >= 5.6
```

## Installation

```bash
$ composer require lucid/cache
```
## Using the storage

```php
<?php

use Lucid\Cache\Storage;
use Lucid\Cache\Client\Filesystem;

$cache = new Storate(new Filesystem('app/caches'));

$cache->set('id', 'value');
$cache->get('id'); // 'value'
```

### Included clients

- `APCu`
- `Filesystem`
- `InMemory`
- `Redis`
- `Memcached`
- `Memcache (php < 7.0)`
- `XCache`
