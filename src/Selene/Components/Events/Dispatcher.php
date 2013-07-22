<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events;

use Closure;
use Selene\Components\DependencyInjection\ContainerInterface;

/**
 * @class Dispatcher
 * @see DispatcherInterface
 *
 * @package Selene\Components\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class Dispatcher implements DispatcherInterface
{
    /**
     * @var string;
     */
    const EVTHANDLER_SEPARATOR = '@';

    /**
     * events
     *
     * @var array;
     */
    private $handlers = [];

    /**
     * observers
     *
     * @var array
     */
    private $observers = [];

    /**
     * sorted
     *
     * @var array
     */
    private $sorted = [];

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * Create a new event dispatcher instance.
     *
     * @param ContainerInterace $container
     *
     * @access public
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Set the ioc container.
     *
     * @param ContainerInterface $container an IoC container implementation
     *
     * @access public
     * @return void
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Attach an eventhandler
     *
     * @param string $event         the event name
     * @param mixed  $eventHandler  a callable or string `class@method`
     * @param int    $priority      the priority with the handler being called
     * (handlers with higher values will be called first)
     *
     * @access public
     * @return void
     */
    public function on($event, $eventHandler, $priority = 10)
    {
        $class = null; $method = null;
        if (!is_callable($eventHandler)) {
            extract($this->getEventHandlerFromClassString($event, $eventHandler));
        }
        $this->bindEvent($event, $eventHandler, $priority, $class, $method);
    }

    /**
     * Attach an eventhandler just once.
     *
     * Unbinds the eventhandler once the event was fired;
     *
     * @param string $event         the event name
     * @param mixed  $eventHandler  a callable or string `class@method`
     * @param int $priority         the priority with the handler being called
     *
     * @access public
     * @return void
     */
    public function once($event, $eventHandler, $priority = 10)
    {
        $class = null; $method = null;
        if (!is_callable($eventHandler)) {
            extract($this->getEventHandlerFromClassString($event, $eventHandler));
        }

        $handler = $eventHandler;

        $eventHandler = function () use ($event, &$handler, &$eventHandler)
        {
            $dispatched = call_user_func_array($handler, func_get_args());
            $this->off($event, $eventHandler);
        };

        $this->bindEvent($event, $eventHandler, $priority, $class, $method);
    }

    /**
     * Detach an eventhandler from an event
     *
     * @param string $event         the event name
     * @param mixed  $eventHanlder  the eventhandler
     *
     * @access public
     * @return void
     */
    public function off($event, $eventHandler = null)
    {
        if (!is_null($eventHandler) and $this->detachEventByHandler($event, $eventHandler)) {
            return;
        }

        unset($this->handlers[$event]);
    }

    /**
     * addObserver
     *
     * @param mixed $observer
     *
     * @access public
     * @return mixed
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber::getSubscriptions() as $event => $handles) {

            foreach ($this->listSubscriptions($subscriber, $event, $handles) as $subscription) {
                call_user_func_array([$this, 'on'], $subscription);
            }
        }
    }

    /**
     * removeSubscriber
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber::getSubscriptions() as $event => $handles) {

            foreach ($this->listSubscriptions($subscriber, $event, $handles) as $subscription) {
                array_pop($subscription);
                call_user_func_array([$this, 'off'], $subscription);
            }
        }
    }

    /**
     * listObserverSubscriptions
     *
     * @param mixed $observer
     * @param mixed $event
     * @param array $eventSubscriptions
     *
     * @access protected
     * @return array
     */
    protected function listSubscriptions($subscriber, $event, array $eventSubscriptions, array &$list = [])
    {
        if (is_array(current($eventSubscriptions))) {
            foreach ($eventSubscriptions as $subscription) {
                $this->listSubscriptions($subscriber, $event, $subscription, $list);
            }
            return $list;
        }

        list($method, $priority) = array_pad($eventSubscriptions, 2, 10);
        $eventHandler = [$subscriber, $method];

        $list[] = compact('event', 'eventHandler', 'priority');

        return $list;
    }

    /**
     * Dispatches an event.
     *
     * @param string $event             the event name
     * @param mixed  $parameters        data to be send along with the event,
     * typically an EventInteface instance
     * @param bool   $stopOnFirstResult stop fireing if first result was found
     *
     * @access public
     * @return array the event results;
     */
    public function dispatch($event, $parameters = [], $stopOnFirstResult = false)
    {
        if (!isset($this->handlers[$event])) {
            return;
        }

        $results = [];
        $this->sort($event);

        if ($isEvent = $parameters instanceof EventInterface) {
            $parameters->setName($event);
        }

        foreach ($this->handlers[$event] as $i => $handler) {

            extract($handler);

            if ($isEvent and $parameters->isPropagationStopped()) {
                break;
            }

            $res = call_user_func_array($eventHandler, !is_array($parameters) ? [$parameters] : $parameters);

            $results[] = $res;

            // stop on first result;
            if (!is_null($res) and $stopOnFirstResult) {
                break;
            }
        }
        return $results;
    }

    /**
     * Dispatches an event until the first response happened.
     *
     * @see Dispatcher#dispatch()
     *
     * @access public
     * @return array
     */
    public function untill($event, $parameters = [])
    {
        return $this->dispatch($event, $parameters, true);
    }

    /**
     * Get event handlers by event name
     *
     * @param string $event the event name
     *
     * @access public
     * @return array
     */
    public function getEventHandlers($event = null)
    {
        if (is_null($event)) {
            $handlers = [];

            foreach ($this->handlers as $handler) {
                $stack = ['stack' => &$handlers] + array_pluck('eventHandler', $handler);
                call_user_func_array('array_push', $stack);
            }
            return $handlers;
        }

        if (isset($this->handlers[$event])) {
            $this->sort($event);
            return array_pluck('eventHandler', $this->handlers[$event]);
        }

        return [];
    }

    /**
     * Bind the Eventhandler to the dispatcher
     *
     * @param string $event       the event name
     * @param mixed $eventHandler a callable
     * @param int $priority       event fire priority
     *
     * @access protected
     * @return void
     */
    protected function bindEvent($event, $eventHandler, $priority = 10, $class = null, $method = null)
    {
        $handler = compact('event', 'priority');
        $handler['eventHandler'] =& $eventHandler;

        if (!is_null($class)) {
            $handler['uses'] = implode(static::EVTHANDLER_SEPARATOR, [$class, $method]);
        }

        unset($this->sorted[$event]);

        $this->handlers[$event][] = $handler;
    }

    /**
     * Sort eventhandler by their priority.
     *
     * @access protected
     * @return mixed
     */
    protected function sort($event)
    {
        if (isset($this->sorted[$event])) {
            return;
        }

        usort($this->handlers[$event], function ($a, $b)
        {
            return $a['priority'] > $b['priority'] ? -1 : 1;
        });

        $this->sorted[$event] = true;
    }

    /**
     * Finds a matching handler an unsets the handler for the given event.
     *
     * @param string $event         the event name
     * @param mixed $eventHandler   the event handler
     *
     * @access protected
     * @return boolean always returns true
     */
    protected function detachEventByHandler($event, $eventHandler)
    {
        if ($isClass = is_string($eventHandler)) {
            $classHandler = implode(
                static::EVTHANDLER_SEPARATOR, $this->extractClass($eventHandler)
            );
        }

        foreach ($this->handlers[$event] as $index => &$handler) {

            if ($isClass and isset($handler['uses']) and $classHandler === $handler['uses']) {
                unset($this->handlers[$event][$index]);
            } elseif ($eventHandler === $handler['eventHandler']) {
                unset($this->handlers[$event][$index]);
            }
        }
        return true;
    }

    /**
     * getEventHandlerFromClassString
     *
     * @param mixed $eventHandler
     *
     * @access protected
     * @return array
     */
    protected function getEventHandlerFromClassString($event, $eventHandler)
    {
        if (is_string($eventHandler)) {
            extract($this->extractClass($eventHandler));

            if (!$this->container) {
                throw new \InvalidArgumentException(
                    sprintf('No container is set yet')
                );
            }

            $eventHandler = function () use ($class, $method)
            {
                return call_user_func_array([$this->container[$class], $method], func_get_args());
            };

            return compact('eventHandler', 'class', 'method');
        }

        throw new \InvalidArgumentException('Eventhandler is invalid');
    }

    /**
     * extractClass
     *
     * @param mixed $eventHandler
     *
     * @access protected
     * @return array
     */
    protected function extractClass($eventHandler)
    {
        list($class, $method) = array_pad(
            explode(static::EVTHANDLER_SEPARATOR, $eventHandler),
            2,
            'handleEvent'
        );

        return compact('class', 'method');
    }
}
