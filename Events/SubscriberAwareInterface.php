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
 * @interface SubscriberAwareInterface
 *
 * @package Selene\Module\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface SubscriberAwareInterface
{
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
