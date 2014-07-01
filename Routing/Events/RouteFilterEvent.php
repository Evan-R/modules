<?php

/**
 * This File is part of the Selene\Components\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Events;

/**
 * @class RouteFilterEvent extends Event
 * @see Event
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class RouteFilterEvent extends RouteEvent
{
    private $response;

    public function setResponse($result)
    {
        $this->stopPropagation();

        $this->response = $result;
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
