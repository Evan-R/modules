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
 * @interface EventHandlerInterface
 *
 * @package Selene\Module\Events
 * @version $Id$
 */
interface EventListenerInterface
{
    public function handleEvent(EventInterface $event);
}
