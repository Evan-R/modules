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

use \Symfony\Component\HttpFoundation\Response;

/**
 * @class ResponseEvent
 * @package Selene\Components\Kernel\Events
 * @version $Id$
 */

abstract class ResponseEvent extends KernelEvent
{
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
