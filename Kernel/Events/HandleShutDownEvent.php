<?php

/**
 * This File is part of the Selene\Components\Kernel\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Events;

use \Selene\Components\Events\Event;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

/**
 * @class HandleShutDownEvent
 * @package Selene\Components\Kernel\Events
 * @version $Id$
 */
class HandleShutDownEvent extends Event
{
    private $request;

    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
