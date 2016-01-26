# A PSR-7 compatible HTTP router

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

use Lucid\Mux\RouteCollectionBuilder as Builder;

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

### Dispatching routes

The router component takes a request context object to dispatch the
corresponding routing action.

```php
<?php
use Lucid\Mux\Router;
use Lucid\Mux\Request\Context as RequestContext;

$router = new Router($builder->getCollection());

$request = new RequestContext(
    current(explode('?', $_SERVER['REQUEST_URI'])),
    $_SERVER['REQUEST_METHOD']
);

$response = $router->dispatch($request);

```

#### Working with PSR-7 requests

You can easily create a requestcontext from an existing psr7 compatible
server request by using the `Context::fromPsrRequest()` method.

```php
<?php
$request = new RequestContext::fromPsrRequest($psrRequest);
```

#### Dispatching named routes

```php
<?php

$options = [
    'id' => 12
];

$response = $router->route('user.delete', $options);
```

### Advanced router configuration

The router mostly relies on two main components:

 1. a handler dispatcher, which is responsible for finding and executing the
     given action (defined on the route object)
 - a response mapper, which is capable of mapping the responsens to a desired
    type

#### The handler dispatcher

By default, the handler dispatcher/resolver will check if the given handler is
callable. If the handler is a string containing an @ symbol, it is assumed that
the left hand side represents a classname and the right hand site a method.

##### Dependency Injection

If the handler resolver (`Lucid\Mux\Handler\Resolver` by default) is constructed
with an instance of `Interop\Container\ContainerInterface` it will also check if
the left hand side is a service registered by the di container.

```php
<?php

use Lucid\Mux\Handler\Resolver;
use Lucid\Mux\Handler\Dispatcher;

$resolver = new Resolver($container)
$dispatcher = new Dispatcher($resolver);
```

#### The response mapper

By default, the response mapper is a simple passthrough mapper. However it's easy
to create a custom mapper that suites your
specific needs.

```php
<?php

use Zend\Diactoros\Response;

use Lucid\Mux\Request\ResponseMapperInterface.php;

class PsrResponseMapper implements ResponseMapperInterface
{
    public function mapResponse($response)
    {
        return new Response($response);
    }
}
```
