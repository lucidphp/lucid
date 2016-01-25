# A PSR-7 compliant HTTP router

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/mux-blue.svg?style=flat-square)](https://github.com/lucidphp/mux/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/mux/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/mux/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/mux)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/mux/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/mux)
[![HHVM](https://img.shields.io/hhvm/lucid/mux/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/mux)

## Installation

```shell
> composer require lucid/mux:dev-develop
```

## Usage

### Creating coute collections

#### Manualy

```php
<?php

use Lucid\Mux\Route;
use Lucid\Mux\Routes;

$routes = new Routes;
$routes->add('index', new Route('/', 'Acme\FrontController@getIndex'));

```

#### Using the Builder

```php
<?php
use Lucid\Mux\Builder;

$builder = new Builder;

// adds a GET route
$builder->get('/', 'Acme\FrontController@getIndex');

// adds a POST route
$builder->post('/user', 'Acme\UserController@createUser');

// adds a UPDATE route
$builder->update('/user/{id}', 'Acme\UserController@updateUser');

// adds a DELETE route
$builder->delete('/user/{id}', 'Acme\UserController@deleteUser');

```

```php
<?php
use Lucid\Mux\Request\Context as RequestContext;

$request = new RequestContext('/', 'GET');
```

```php
<?php
$request = new RequestContext::fromPsrRequest($psrRequest);
```

```php
<?php
use Lucid\Mux\Router;
use Lucid\Mux\Request\Context as RequestContext;

$router = new Router($builder->getCollection());

$request = new RequestContext('/', 'GET');

$response = $router->dispatch($request);

```

```php
<?php

$options = [
    'id' => 12
];

$response = $router->route('user.delete', $options);
```
