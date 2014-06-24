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
     * until
     *
     * @param mixed $event
     * @param mixed $parameter
     *
     * @access public
     * @return mixed
     */
    public function until($event, $parameter = []);

    /**
     * getEventHandlers
     *
     * @param mixed $event
     *
     * @access public
     * @return mixed
     */
    public function getEventHandlers($event = null);
}
