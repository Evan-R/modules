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
interface DispatcherInterface
{
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
     * @access public
     * @return void
     */
    public function once($event, $eventHanlder);

    /**
     * off
     *
     * @param mixed $event
     * @param mixed $eventHanlder
     *
     * @access public
     * @return void
     */
    public function off($event, $eventHanlder = null);

    /**
     * dispatch
     *
     * @param mixed $event
     * @param mixed $parameter
     *
     * @access public
     * @return array
     */
    public function dispatch($event, $parameter = []);

    /**
     * untill
     *
     * @param mixed $event
     * @param mixed $parameter
     *
     * @access public
     * @return mixed
     */
    public function untill($event, $parameter = []);

    /**
     * getEventHandlers
     *
     * @param mixed $event
     *
     * @access public
     * @return mixed
     */
    public function getEventHandlers($event = null);

    /**
     * addSubscriber
     *
     * @param SubscriberInterface $subscriber
     *
     * @access public
     * @return mixed
     */
    public function addSubscriber(SubscriberInterface $subscriber);

    /**
     * renoveSubscriber
     *
     * @param SubscriberInterface $subscriber
     *
     * @access public
     * @return mixed
     */
    public function removeSubscriber(SubscriberInterface $subscriber);

}
