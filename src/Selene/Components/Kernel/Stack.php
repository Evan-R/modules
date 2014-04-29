<?php

/**
 * This File is part of the Selene\Components\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpKernel\TerminableInterface;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Events\DispatcherAwareInterface;
use \Selene\Components\Events\SubscriberAwareInterface;

/**
 * @class Stack implements HttpKernelInterface, TerminableInterface
 * @see HttpKernelInterface
 * @see TerminableInterface
 *
 * @package Selene\Components\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Stack implements HttpKernelInterface, TerminableInterface
{
    /**
     * app
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    /**
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     *
     * @access public
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * handleRequest
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $type
     * @param boolean $catch
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this->getKernel()->handle($request, $type, $catch);
    }

    /**
     * terminate
     *
     * @param \Symfony\Component\HttpFoundation\Request  $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @access public
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        foreach ($this->middlewares as $middleware) {
            if ($middleware instanceof TerminableInterface) {
                $middleware->terminate($request, $response);
            }
        }
    }

    /**
     * getKernel
     *
     * @access protected
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected function getKernel()
    {
        return $this->kernel;
    }
}
