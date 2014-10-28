# Event dispatcher library

## Usage

```php
<?php

use Lucid\Module\Event\EventInterface;
use Lucid\Module\Event\EventDispatcher;

$dispatcher = new EventDispatcher;

$dispatcher->addHandler('my_event', function (EventInterface $event) {
	// do something
});
```

## Event Handlers

Eventhandlers can be any callable but must accept an instance of `EventInterface`
as their first argument.

Using handlers the implement the `HandlerInterface` will automatically call the `handleEvent` method on the handler if the event is dispatched.

```php
<?php

use Lucid\Module\Event\EventInterface;
use Lucid\Module\Event\HandlerInterface;
use Lucid\Module\Event\EventDispatcher;

class MyHandler implements HandlerInterface
{
	public function handleEvent(EventInterface $event)
	{
		// doo something
	}
}
```

## Custom Events

Event objects can be referred to message objects. You can easily create your
custom message objects by implementing the `EventInterface` interface or
extending from `Event`.

```php
<?php

namespace Acme\Message;

use Lucid\Module\Event\Event;

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
