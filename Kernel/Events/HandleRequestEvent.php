<?php

/**
 * This File is part of the \Users\malcolm\www\selene_source\src\Selene\Components\Kernel\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Events;

use \Symfony\Component\HttpFoundation\Response;

/**
 * @class HandleRequestEvent
 * @package \Users\malcolm\www\selene_source\src\Selene\Components\Kernel\Events
 * @version $Id$
 */
class HandleRequestEvent extends KernelEvent
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
