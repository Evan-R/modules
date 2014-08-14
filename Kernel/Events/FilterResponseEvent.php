<?php

/**
 * This File is part of the Selene\Module\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel\Events;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface as Kernel;

/**
 * @class FilterResponseEvent
 * @see KernelEvent
 * @package Selene\Module\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FilterResponseEvent extends ResponseEvent
{
    /**
     * Creates a new FilterResponse event.
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Kernel $kernel, Request $request, $type, Response $response)
    {
        parent::__construct($kernel, $request, $type);

        $this->setResponse($response);
    }
}
