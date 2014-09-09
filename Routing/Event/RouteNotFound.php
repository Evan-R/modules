<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Event;

use \Selene\Module\Events\Event;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteNotFoundEvent extends Event
 * @see Event
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RouteNotFound extends Event
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * getRequest
     *
     * @access public
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
