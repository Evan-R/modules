<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events\Traits;

use \Selene\Components\Events\DispatcherInterface;

/**
 * @trait SubscriberTrait
 *
 * @package Selene\Components\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
trait SubscriberTrait
{
    /**
     * getSubscriptions
     *
     *
     * @access public
     * @return array
     */
    public function getSubscriptions()
    {
        return static::$subscriptions;
    }

    /**
     * subscribeTo
     *
     * @param DispatcherInterface $dispatcher
     *
     * @return void
     */
    public function subscribeTo(DispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($this);
    }
}
