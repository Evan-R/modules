# Selene PubSub Component 

[![Build Status](https://api.travis-ci.org/seleneapp/events.png?branch=development)](https://travis-ci.org/seleneapp/events)
[![Latest Stable Version](https://poser.pugx.org/selene/events/v/stable.png)](https://packagist.org/packages/selene/events) 
[![Latest Unstable Version](https://poser.pugx.org/selene/events/v/unstable.png)](https://packagist.org/packages/selene/events) 
[![License](https://poser.pugx.org/selene/events/license.png)](https://packagist.org/packages/selene/events)
[![HHVM Status](http://hhvm.h4cc.de/badge/selene/events.png)](http://hhvm.h4cc.de/package/selene/events)

[![Coverage Status](https://coveralls.io/repos/seleneapp/events/badge.png?branch=development)](https://coveralls.io/r/seleneapp/events?branch=development)
[![Code Climate](https://codeclimate.com/github/seleneapp/events.png)](https://codeclimate.com/github/seleneapp/events)

## Installation

Installation is done via [composer](https://getcomposer.org).

Add `selene/events` as requirement to your `composer.json` file.

```json
{
	"require": {
		"selene/events":"dev-development"
	}
}
```

Run `composer install` or `composer update`

```bash
$ composer install --dev
```

## Testing

Run tests with: 

```bash
$ vendor/bin/phpunit
```

## Setup

```php
<?php

use Selene\Components\Events;

$events = new Dispatcher;
```

## Usage

### The Event Dipatcher

#### Attaching event handlers

Event handlers can be any valid callable objects. If you use the dispatcher with a
[DIC][1] (Dependency Injection Container), attaching services as handler is also possible. e.g.
`myservice@handleStuff` or just `myservice` if the services implements
`EventListenerInterface`.

```php
<?php

$events->on('event_name', $callback);

$events->on('event_name', function () {
	// …
});

$events->on('event_name', [$object, 'method']);

$events->on('event_name', 'Static::method');

```

You can also attach event listeners. An event listener must implement `EventListenerInterface`.

```php
<?php

$events->addListener('event_name', $eventListener);
// same as
$events->on('event_name', $eventListener);

```

#### Execution priority

`Dispatcher::on()` as well as `Dispatcher::addListener` take an integer value as third argument, specifiying the
execution priority of the handler callback. A callback with a higher priority value will be executed before a lower one.

```php
<?php

$events->on('event_name', $callbackBefore, 10);

$events->on('event_name', $callbackMid, 5);

$events->on('event_name', $callbackAfter, 0);
```
--------

#### Attaching event handlers only once

You can attach an event handler to execute excaclty one time. The handler will
detach itself after being called the first time. 

```php
<?php

$events->once('event_name', $callback);

```

#### Detaching event handlers

You can either coose to remove a certain listener from a given event, or remove
all listeners for an event.

If no event handler is given, all events that are registered under the
given event name will be cancled.

```php
<?php

// will cancel all eventhandlers for `event_name`

$events->off('event_name');

// will detach `$callback` for `event_name`
// other handlers bound to `event_name` remain untouched
$events->off('event_name', $callback);

```

#### Using the Event Dispatcher with Services

It is possible to use the event dispatcher with the [service
container][1].

```php
<?php

use Selene\Component\Events\Dispatcher;
use Selene\Component\DI\Container;

$container = new Container;

$events = new Dispatcher($container);

//… or set the container:

$events = new Dispatcher($container);
$events->setContainer($container);


```
You may annotate the handle method on the service id using the `@` symbol, lik
`mysevice@mycallback`. If your service implements `EventListenerInterface`, the handle annotation may be ommitted. 

```php
<?php
$events->on('event_name', 'my_listener'); // assuming SeviceListener implements
`EventListenerInterface`
```

If you want to target a different handler method append `@` followed my the
method name to the service id

```php
<?php
$events->on('event_name', 'my_service@eventCallbackMethod'); 
```
---------

#### Dispatching events

The dispatch method takes three arguments `$event_name`, `$event`, and
`$stopOnFirstResult`. The second an third arguments are optional. If no event is passed in as a second argument, the dispatcher will create

```php
<?php

use \Selene\Components\Event\Event;

$event = new Event;
$events->dispatch('event_name', $event); // returns an array containing the results returned by the attached handlers.

// will stop broadcasting the event as  soon as the first result is being returned:
$events->dispatch('event_name', $event, true); 
// same as
$events->until('event_name', $event); 
```

After being dispatched, the event object will always have a name property
equally to the the event name. Also, the dispatcher instance will be avaliable
calling `Event::getDispatcher()`.

##### Custom events

Of course you can create your onw custom event objects.

```php
<?php

namespace Acme\Eventing

use \Selene\Components\Events\Event.

class AcmeEvent extends Event
{
	public function __construct(…)
	{
		// do your setup
	}
}
```

---------

#### Get all attached handlers

```php
<?php

// returns an array of all attached handlers
$events->getEventHandlers(); 

// returns an array of all handlers attached to `my_event`
$events->getEventHandlers('my_event'); 
```

---------

#### Event Subscribers

Event Subscribers are utility classes that can subscibe to multiple events
attaching one or more eventhandlers per event at once. 

An Event Subscriber must implement the [`Selene\Components\Event\SubscriberInterface`](https://github.com/seleneapp/events/blob/development/SubscriberInterface.php).

```php
<?php

namespace Acme\Foo;

use \Selene\Components\Events\EventInterface;
use \Selene\Components\Events\SubscriberInterface;

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

    public function onFooEventPre(EventInterface $event)
    {
        return $event->getEventName() . '.pre';   // 'foo_event.pre'
    }

    public function onFooEventMid(EventInterface $event)
    {
        return $event->getEventName() . '.mid';   // 'foo_event.mid' 
    }

    public function onFooEventAfter(EventInterface $event)
    {
        return $event->getEventName() . '.after'; // 'foo_event.after'
    }

    public function onBarEvent(EventInterface $event)
    {
        return $event->getEventName();            // 'bar_event'
    }
}
```

Now add the subscriber to the event dispatcher.

```php
<?php

$subscriber = new Acme\Foo\EventSubscriber;
$events->addSubscriber($subscriber);

$events->dispatch('foo_event');  // => ['foo_event.pre', 'foo_event.mid', 'foo_event.after']
$events->dispatch('bar_event');  // => ['bar_event']

```

Removing a subscriber

```php
<?php
$events->removeSubscriber($subscriber);
```

### The Event Queue Dispatcher

The `EventQueueDispatcher` can register eventlisteners and dispatch a array of
events directly. By default, and if the event object inherits from `Selene\Components\Events\Event`, the event name will match your event class name, where the camelcase notation is divided by a dot (e.g. `FooEvent` becomes `foo.event`). 

Otherwhise be shure to set the event name on your event object before dispatching it throut the `EventQueueDispatcher`.

```php
<?php

use \Acme\Eventing\Event;
use \Acme\Eventing\EmergencyEvent;
use \Acme\Eventing\Listeners\EventListener;
use \Acme\Eventing\Listeners\EmergencyListener;
use \Selene\Components\Events\EventQueueDispatcher;


$dispatcher = new EventQueueDispatcher;

$eventListener = new EventListener;
$emergencyListener = new EmergencyListener;
$event = new QueueListener;

$dispatcher->addListener('event', $eventListener, 100); 
$dispatcher->addListener('emergency.event', $emergencyListener, 100); 

$events = [
	new Event,
	new EmergencyEvent
];

$dispatcher->dispatch($events);

```

[1]: https://github.com/seleneapp/di
