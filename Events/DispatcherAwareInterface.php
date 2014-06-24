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
 * @interface EventAwareInterface
 * @package Selene\Components\Events
 * @version $Id$
 */
interface DispatcherAwareInterface
{
    /**
     * setDispatcher
     *
     * @param DispatcherInterface $events
     *
     * @access public
     * @return void
     */
    public function setDispatcher(DispatcherInterface $events);

    /**
     * getDispatcher
     *
     * @param DispatcherInterface $events
     *
     * @access public
     * @return \Selene\Components\Events\DispatcherInterface
     */
    public function getDispatcher();
}
