# Event Dispatcher Library

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/signal-blue.svg?style=flat-square)](https://github.com/lucidphp/signal/tree/develop)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/lucidphp/signal/blob/develop/LICENSE.md)

[![Build Status](https://img.shields.io/travis/lucidphp/signal/develop.svg?style=flat-square)](https://travis-ci.org/lucidphp/signal)
[![Code Coverage](https://img.shields.io/coveralls/lucidphp/signal/develop.svg?style=flat-square)](https://coveralls.io/r/lucidphp/signal)
[![HHVM](https://img.shields.io/hhvm/lucid/signal/dev-develop.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/signal)	

## Requirements

```
php >= 5.6
```

## Installation

You may install `lucid/signal` with composer.

```bash
$ composer require lucid/signal --save
```

## Usage

```php
<?php

use Lucid\Signal\EventInterface;
use Lucid\Signal\EventDispatcher;

$dispatcher = new EventDispatcher;

$dispatcher->addHandler('my_event', function (EventInterface $event) {
	// do something
});
```

### Event Handlers

Eventhandlers can be any callable but must accept an instance of `EventInterface`
as their first argument.

Using handlers the implement the `HandlerInterface` will automatically call the `handleEvent` method on the handler if the event is dispatched.

```php
<?php

use Lucid\Signal\EventInterface;
use Lucid\Signal\HandlerInterface;
use Lucid\Signal\EventDispatcher;

class MyHandler implements HandlerInterface
{
	public function handleEvent(EventInterface $event)
	{
		// do something
	}
}
```

```php
<?php

$dispatcher = new EventDispatcher;
$handler = new MyHandler;

$dispatcher->addHandler('my_event', $handler);

```

`MyHandler::handleEvent` will now be called when `my_event` is fired.

### Event Delegation

Events are fired subsequentially unless all handlers where adressed or until
the Event object is being stopped. You can stop the eventdelegation in your
handler by calling `$event->stop()`.

### Custom Events

Event objects can be referred to message objects. You can easily create your
custom message objects by implementing the `EventInterface` interface or
extending the `Event` base class.

```php
<?php

namespace Acme\Message;

use Lucid\Signal\Event;

class SysMessage extends Event
{
	private $message;

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
	}
}
```
