<?php

/**
 * This File is part of the Selene\Module\Kernel\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel\Events;

use \Selene\Module\Events\Event;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpKernel\HttpKernelInterface as Kernel;

/**
 * @class KernelEvent
 * @package Selene\Module\Kernel\Events
 * @version $Id$
 */
abstract class KernelEvent extends Event
{
    private $kernel;
    private $request;
    private $requestType;

    public function __construct(Kernel $kernel, Request $request, $type)
    {
        $this->kernel = $kernel;
        $this->request = $request;
        $this->requestType = $type;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getKernel()
    {
        return $this->kernel;
    }

    public function getRequestType()
    {
        return $this->requestType;
    }
}
