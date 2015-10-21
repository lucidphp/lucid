# Cache component for selene.
[![Build Status](https://api.travis-ci.org/seleneapp/cache.png?branch=development)](https://travis-ci.org/seleneapp/cache)
[![Code Climate](https://codeclimate.com/github/seleneapp/cache.png)](https://codeclimate.com/github/seleneapp/cache)
[![Coverage Status](https://coveralls.io/repos/seleneapp/cache/badge.png?branch=development)](https://coveralls.io/r/seleneapp/cache?branch=development)

[![License](https://poser.pugx.org/selene/config/license.png)](https://packagist.org/packages/selene/config)

## Installation

The component can be installed via [composer][composer].

```json
{
	"require":{
		"selene/cache":"dev-development"
	}
}
```
Then run 

```bash
$ composer install
```
## Using the storage

```php
<?php

use \Selene\Components\Cache\Storage;

$cache = new Storage($driver);

```

### Drivers

Available drivers are 

#### ArrayDriver

```php
<?php

use \Selene\Components\Cache\Driver\ArrayDriver;

$driver = new ArrayDriver;

```
#### ApcDriver

```php
<?php

use \Selene\Components\Cache\Driver\ApcDriver;

$driver = new ApcDriver;

```
#### ApcuDriver

```php
<?php

use \Selene\Components\Cache\Driver\ApcuDriver;

$driver = new ApcuDriver;

```
#### FilesytemDriver

```php
<?php

use \Selene\Components\Cache\Filesystem\Filesystem;
use \Selene\Components\Cache\Driver\FilesystemDriver;

$driver = new FilesystemDriver(new Filesystem, $path);
```
#### MemcacheDriver

```php
<?php

use \Memcache;
use \Selene\Components\Cache\Driver\MemcacheDriver;
use \Selene\Components\Cache\Driver\MemcacheConnection;

$servers = [['host' => …, 'port' => …, 'weight' => …]];

$driver = new MemcacheDriver(new MemcacheConnection(new Memcache, $servers));

```

#### MemcachedDriver

```php
<?php

use \Memcached;
use \Selene\Components\Cache\Driver\MemcachedDriver;
use \Selene\Components\Cache\Driver\MemcachedConnection;

$servers = [['host' => …, 'port' => …, 'weight' => …]];

$driver = new MemcachedDriver(new MemcachedConnection(new Memcached, $servers));

```

## Usage

```php
<?php

use Selene\Components\Cache\Storage;

$cache->set('key', $cacheValue, 1000);

$cache->get('key');
```

```php
<?php

$cache->set('key', $cacheValue, 1000);

```

```php
<?php

$cache->section('key')->set('foo', 'bar');

```
[composer]: https://getcomposer.org
