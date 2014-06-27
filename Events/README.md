# Selene PubSub Component 

[![Build Status](https://api.travis-ci.org/seleneapp/events.png?branch=development)](https://travis-ci.org/seleneapp/events)
[![Latest Stable Version](https://poser.pugx.org/selene/events/v/stable.png)](https://packagist.org/packages/selene/events) 
[![Latest Unstable Version](https://poser.pugx.org/selene/events/v/unstable.png)](https://packagist.org/packages/selene/events) 
[![License](https://poser.pugx.org/selene/events/license.png)](https://packagist.org/packages/selene/events)
[![HHVM Status](http://hhvm.h4cc.de/badge/selene/events.png)](http://hhvm.h4cc.de/package/selene/events)

[![Coverage Status](https://coveralls.io/repos/seleneapp/events/badge.png?branch=development)](https://coveralls.io/r/seleneapp/events?branch=development)

## Setup

```php
<?php

use Selene\Components\Events;

$events = new Dispatcher;
```

## Usage

### Attaching event handlers

And event handler can be any valid callable (`is_callable()` must return true)

```php
<?php

$events->on('event_name', $callback);

$events->on('event_name', function () {
	// …
});

$events->on('event_name', [$object, 'method']);

$events->on('event_name', 'Static::method');

```

### Handler priority

`Dispatcher::on()` takes an integer value as third argument, specifiying the
handler priority. A higher priority will be executed before a lower one.

```php
<?php

$events->on('event_name', $callbackBefore, 10);

$events->on('event_name', $callbackMid, 5);

$events->on('event_name', $callbackAfter, 0);
```
--------

### Detaching event handlers

```php
<?php

// will cancel all eventhandlers for `event_name`

$events->off('event_name');

// will detach `$callback` for `event_name`
// other handlers bound to `event_name` remain untouched
$events->off('event_name', $callback);

```

### Attaching event handlers only once

You can attach an event handler to execute excaclty one time (the first time
the event is fired after the handler has been attached)

```php
<?php

$events->once('event_name', $callback);

```

### Using the Event Dispatcher with Services

It is possible to use the event dispatcher with the [service
container](https://github.com/seleneapp/dependency-injection).

```php
<?php

use Selene\Component\Events\Dispatche;
use Selene\Component\DI\Container;

$container = new Container;

$events = new Dispatcher($container);

//… or set the container:

$events = new Dispatcher($container);
$events->setContainer($container);


```
There're two ways to attach a service as an eventhandler. By default, the
dispatcher assumes that your service has a handler method called `handleEvent`.
I this case, attaching the service id will suffice.

Note: the serice id must be prepended with a `$` symbol.

```php
<?php
$events->on('event_name', '$my_service'); // assuming ServiceClass::handleEvent() is available
```

If you want to target a different handler method append `@` followed my the
method name to the service id

```php
<?php
$events->on('event_name', '$my_service@eventCallbackMethod'); 
```
---------

### Broadcasting an event

```php
<?php

$parameters = ['foo', 'bar'];
$events->dispatch('event_name', $parameters); // returns an array containing the results returned by the attached handlers.

// will stop broadcasting the event as  soon as the first result is being returned:
$events->dispatch('event_name', $parameters, true); 
// or
$events->until('event_name', $parameters); 
```
---------

### Get all attached handlers

```php
<?php

// returns an array of all attached handlers
$events->getEventHandlers(); 

// returns an array of all handlers attached to `my_event`
$events->getEventHandlers('my_event'); 
```

---------

### Event Subscribers

Event Subscribers are utility classes that can subscibe to multiple events
attaching one or more eventhandlers per event at once. 

An Event Subscriber must implement the [`Selene\Components\Event\SubscriberInterface`](https://github.com/seleneapp/events/blob/development/SubscriberInterface.php).

```php
<?php

namespace Acme\Foo;

use Selene\Components\Events\SubscriberInterface;

class EventSubscriber implements SubscriberInterface
{
    public static $event;

    public function getSubscriptions()
    {
        return [
            'foo_event' => [
                ['onFooEventPre', 100],
                ['onFooEventMid', 10],
                ['onFooEventAfter', 0]
            ],
            'bar_event' => ['onBarEvent', 10]
        ];
    }

    public function onFooEventPre()
    {
        return 'foo.pre';
    }

    public function onFooEventMid()
    {
        return 'foo.mid';
    }

    public function onFooEventAfter()
    {
        return 'foo.after';
    }

    public function onBarEvent()
    {
        return 'bar';
    }
}
```

```php
<?php

$subscriber = new Acme\Foo\EventSubscriber;
$events->addSubscriber($subscriber);

$events->dispatch('foo_event');  // => ['foo.pre', 'foo.mid', 'foo.after']
$events->dispatch('bar_event');  // => ['bar']

// if you need to remove a subscriber, call:
$events->removeSubscriber($subscriber);
```
