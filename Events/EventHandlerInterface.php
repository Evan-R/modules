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
 * @interface EventHandlerInterface
 *
 * @package \Users\malcolm\www\selene_framework\src\Selene\Components\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
interface EventHandlerInterface
{
    public function handleEvent();
}
