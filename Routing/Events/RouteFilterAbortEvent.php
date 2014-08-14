<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Events;

/**
 * @class RouterFilterAbortEvent
 * @package Selene\Module\Routing\Events
 * @version $Id$
 */
class RouteFilterAbortEvent extends RouteDispatchEvent
{
    public function __construct($result = null)
    {
        $this->setResponse($result);
    }
}
