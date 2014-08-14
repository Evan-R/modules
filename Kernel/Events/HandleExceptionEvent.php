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
 * @class KernelEvent
 * @package Selene\Module\Kernel
 * @version $Id$
 */
class HandleExceptionEvent extends ResponseEvent
{
    /**
     * exception
     *
     * @var \Exception
     */
    private $exception;

    /**
     * Create a new HandleExceptionEvent event.
     *
     * @param Request $request
     * @param \Exception $exception
     */
    public function __construct(Kernel $kernel, Request $request, $type, \Exception $exception)
    {
        $this->exception = $exception;
        parent::__construct($kernel, $request, $type);
    }

    /**
     * getException
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
