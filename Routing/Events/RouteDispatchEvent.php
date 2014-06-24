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
 * @class RouteDispatchEvent
 * @package Selene\Components\Routing\Events
 * @version $Id$
 */
class RouteDispatchEvent extends RouteEvent
{
    private $response;

    /**
     * setResponse
     *
     * @param mixed $response
     *
     * @access public
     * @return mixed
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * getResponse
     *
     *
     * @access public
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
