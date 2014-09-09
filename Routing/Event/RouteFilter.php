<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Event;

/**
 * @class RouteFilterEvent extends Event
 * @see Event
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RouteFilter extends RouteEvent
{
    private $response;

    public function setResponse($result)
    {
        if (null !== $result) {
            $this->stopPropagation();
            $this->response = $result;
        }
    }

    /**
     * getResult
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
