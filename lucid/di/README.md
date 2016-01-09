# Dependency Injection container (interop container).

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/signal-blue.svg?style=flat-square)](https://github.com/lucidphp/di/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/di/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/di/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/di)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/di/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/di)
[![HHVM](https://img.shields.io/hhvm/lucid/di/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/di)

## Installation

```bash
$ composer require lucid/di:dev-develop
```

## Requirements

[container-interop/container-interop](https://packagist.org/packages/container-interop/container-interop)


## The Container

### A simple container
```php
<?php

use Lucid\DI\Container;

$container->set('foo', new Acme\Foo);

$container->get('foo'); // instance of Acme\Foo
```

### Aliasing ids

```php
<?php
$container->setAlias('foo', 'bar');

$container->get('bar'); // instance of Acme\Foo
```

### Overriding services

```php
<?php

// throws `Interop\Container\Exception\ContainerException`
$container->set('foo', new Acme\Bar);  

// use `replace` instead
$container->replace('foo', new Acme\Bar);  

// or use `$forcereplace`
$container->set('foo', new Acme\Bar, Container::FORCE_REPLACE_ON_DUPLICATE);  
```

## The Container Builder

```php
<?php

use Lucid\DI\ContainerBuilder;
use Lucid\DI\Definition\Service;

$container = new ContainerBuilder;

// define a service by id and class
$service = $container->define('foo', 'Acme\Foo');

// You can configure the service in detail
$service->...

// basically the same
$container->setService('foo', $service = new Service('Acme\Foo'));
```
