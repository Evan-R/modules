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

/**
 * @interface DispatcherInterface
 *
 * @package Selene\Components\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
interface DispatcherInterface extends SubscriberAwareInterface
{
    /**
     * @var string;
     */
    const EVTHANDLER_SEPARATOR = '@';

    /**
     * addListener
     *
     * @param string $event
     * @param EventListenerInterface $listener
     * @param int $priority
     *
     * @return void
     */
    public function addListener($event, EventListenerInterface $listener, $priority = 0);

    /**
     * on
     *
     * @param mixed $
     *
     * @access public
     * @return void
     */
    public function on($event, $eventHanlder);

    /**
     * once
     *
     * @param mixed $event
     * @param mixed $eventHanlder
     *
     * @return void
     */
    public function once($event, $eventHanlder);

    /**
     * off
     *
     * @param mixed $event
     * @param mixed $eventHanlder
     *
     * @return void
     */
    public function off($event, $eventHanlder = null);

    /**
     * dispatch
     *
     * @param mixed $event
     * @param mixed $parameter
     *
     * @return array
     */
    public function dispatch($eventName, EventInterface $event = null, $stopOnFirstResult = false);

    /**
     * until
     *
     * @param mixed $event
     * @param mixed $parameter
     *
     * @return mixed
     */
    public function until($eventName, EventInterface $event = null);

    /**
     * getEventHandlers
     *
     * @param mixed $event
     *
     * @return array
     */
    public function getEventHandlers($event = null);
}
