# Event Dispatcher Library

[![Author](http://img.shields.io/badge/author-iwyg-blue.svg?style=flat-square)](https://github.com/iwyg)
[![Source Code](http://img.shields.io/badge/source-lucid/event-blue.svg?style=flat-square)](https://github.com/iwyg/event/tree/master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/iwyg/jitimage/blob/develop/LICENSE.md)  

[![Build Status](https://img.shields.io/travis/iwyg/event/master.svg?style=flat-square)](https://travis-ci.org/iwyg/event)
[![Code Coverage](https://img.shields.io/coveralls/iwyg/event/master.svg?style=flat-square)](https://coveralls.io/r/iwyg/event)
[![HHVM](https://img.shields.io/hhvm/lucid/event/master.svg?style=flat-square)](http://hhvm.h4cc.de/package/lucid/event)

## Installation

Require `lucid/event` in your `composer.json` file.

```json
{
    "require": {
        "lucid/event":"dev-master"
    }
}
```

Then run

```bash

$ composer install
```

or

```bash

$ composer update
```

## Usage

```php
<?php

use Lucid\Event\EventInterface;
use Lucid\Event\EventDispatcher;

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

use Lucid\Event\EventInterface;
use Lucid\Event\HandlerInterface;
use Lucid\Event\EventDispatcher;

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

use Lucid\Event\Event;

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
