<?php

/**
 * This File is part of the Selene\Module\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Events;

/**
 * @interface EventAwareInterface
 * @package Selene\Module\Events
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
     * @return \Selene\Module\Events\DispatcherInterface
     */
    public function getDispatcher();
}
